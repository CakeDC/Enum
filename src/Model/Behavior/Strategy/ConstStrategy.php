<?php
declare(strict_types=1);

/**
 * Copyright 2015 - 2023, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2023, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Model\Behavior\Strategy;

use ArrayObject;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Entity;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use CakeDC\Enum\Model\Behavior\Exception\InvalidAliasListException;
use ReflectionClass;

class ConstStrategy extends AbstractStrategy
{
    /**
     * Constants list
     *
     * @var array|null
     */
    protected ?array $constants = null;

    /**
     * {@inheritDoc}
     *
     * @param string $alias Strategy's alias.
     * @param \Cake\ORM\Table $table Table object.
     */
    public function __construct(string $alias, Table $table)
    {
        parent::__construct($alias, $table);
        $this->_defaultConfig['prefix'] = strtoupper($alias);
        $this->_defaultConfig['lowercase'] = false;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $config List of callable filters to limit items generated from list.
     * @return array
     * @throws \ReflectionException
     */
    public function enum(array $config = []): array
    {
        $constants = $this->getConstants();
        $keys = array_keys($constants);

        foreach ($config as $callable) {
            if (is_callable($callable)) {
                $keys = array_filter($keys, $callable);
            }
        }

        $values = array_map(fn ($v): mixed => $constants[$v], $keys);

        return array_combine($keys, $values);
    }

    /**
     * Returns defined constants for the current `$_table`.
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function getConstants(): array
    {
        if ($this->constants !== null) {
            return $this->constants;
        }

        $prefix = $this->getConfig('prefix');
        $lowercase = $this->getConfig('lowercase');
        $className = $this->getConfig('className') ?: get_class($this->table);
        $length = strlen($prefix) + 1;
        $classConstants = (new ReflectionClass($className))->getConstants();
        $constants = [];

        foreach ($classConstants as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $listKey = substr($key, $length);
                if ($lowercase) {
                    $listKey = strtolower($listKey);
                }
                $constants[$listKey] = $value;
            }
        }

        return $this->constants = $constants;
    }

    /**
     * @param \Cake\Event\EventInterface $event The beforeFind event that was fired.
     * @param \Cake\ORM\Query\SelectQuery $query Query
     * @param \ArrayObject $options The options for the query
     * @return void
     */
    public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options): void
    {
        $assocName = Inflector::pluralize(Inflector::classify($this->alias));
        if ($this->table->hasAssociation($assocName)) {
            throw new InvalidAliasListException([$this->alias, $this->table->getAlias(), $assocName]);
        }

        $contain = array_filter(
            $query->getContain(),
            fn ($value): bool => $value !== $assocName,
            ARRAY_FILTER_USE_KEY
        );

        $query->clearContain()->contain($contain);

        $query->formatResults(fn (CollectionInterface $results) => $results
            ->map(function (EntityInterface $row): EntityInterface {
                $constant = Hash::get($row, $this->getConfig('field'));

                $field = Inflector::singularize(Inflector::underscore($this->alias));
                $value = new Entity([
                    'label' => Hash::get($this->getConstants(), $constant, $constant),
                    'prefix' => $this->getConfig('prefix'),
                    'value' => $constant,
                ], ['markClean' => true, 'markNew' => false]);

                $row->set($field, $value);
                $row->setDirty($field, false);

                return $row;
            }));
    }
}
