<?php
declare(strict_types=1);

/**
 * Copyright 2015 - 2019, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2019, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Model\Behavior\Strategy;

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
     * @var array
     */
    protected $_constants;

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
     */
    public function enum(array $config = []): array
    {
        $constants = $this->_getConstants();
        $keys = array_keys($constants);

        foreach ($config as $callable) {
            if (is_callable($callable)) {
                $keys = array_filter($keys, $callable);
            }
        }

        $values = array_map(function ($v) use ($constants) {
            return $constants[$v];
        }, $keys);

        return array_combine($keys, $values);
    }

    /**
     * Returns defined constants for the current `$_table`.
     *
     * @return array
     */
    protected function _getConstants(): array
    {
        if ($this->_constants !== null) {
            return $this->_constants;
        }

        $prefix = $this->getConfig('prefix');
        $lowercase = $this->getConfig('lowercase');
        $className = $this->getConfig('className') ?: get_class($this->_table);
        $length = strlen($prefix) + 1;
        $classConstants = (new ReflectionClass($className))->getConstants();
        $constants = [];

        foreach ($classConstants as $key => $value) {
            if (strpos($key, (string)$prefix) === 0) {
                $listKey = substr($key, $length);
                if ($lowercase) {
                    $listKey = strtolower($listKey);
                }
                $constants[$listKey] = $value;
            }
        }

        return $this->_constants = $constants;
    }

    /**
     * @param \Cake\Event\EventInterface $event The beforeFind event that was fired.
     * @param \Cake\ORM\Query $query Query
     * @param \ArrayObject $options The options for the query
     * @return void
     */
    public function beforeFind(\Cake\Event\EventInterface $event, \Cake\ORM\Query $query, \ArrayObject $options)
    {
        $assocName = Inflector::pluralize(Inflector::classify($this->_alias));
        if ($this->_table->hasAssociation($assocName)) {
            throw new InvalidAliasListException([$this->_alias, $this->_table->getAlias(), $assocName]);
        }

        $contain = array_filter($query->getContain(), function ($value) use ($assocName) {
            return $value !== $assocName;
        }, ARRAY_FILTER_USE_KEY);

        $query->clearContain()->contain($contain);

        $query->formatResults(function (\Cake\Collection\CollectionInterface $results) {
            return $results->map(function ($row) {
                if (is_string($row) || !$row) {
                    return $row;
                }

                $constant = Hash::get($row, $this->getConfig('field'));

                $field = Inflector::singularize(Inflector::underscore($this->_alias));
                $value = new \Cake\ORM\Entity([
                    'label' => Hash::get($this->_getConstants(), $constant, $constant),
                    'prefix' => $this->getConfig('prefix'),
                    'value' => $constant,
                ], ['markClean' => true, 'markNew' => false]);

                if (is_array($row)) {
                    $row[$field] = $value->toArray();

                    return $row;
                }

                $row->set($field, $value);
                $row->setDirty($field, false);

                return $row;
            });
        });
    }
}
