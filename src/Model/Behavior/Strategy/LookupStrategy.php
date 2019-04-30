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

use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

class LookupStrategy extends AbstractStrategy
{
    use LocatorAwareTrait;
    use ModelAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param string $alias Strategy's alias.
     * @param \Cake\ORM\Table $table Table object.
     */
    public function __construct(string $alias, Table $table)
    {
        parent::__construct($alias, $table);
        $this->modelClass = 'CakeDC/Enum.Lookups';
        $this->modelFactory('Table', [$this->getTableLocator(), 'get']);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $config (unused in this case).
     * @return array
     */
    public function enum(array $config = []): array
    {
        $query = $this->loadModel()
            ->find('list', [
                'keyField' => 'name',
                'valueField' => 'label',
            ])
            ->where([
                'prefix' => $this->getConfig('prefix'),
            ]);

        foreach ($config as $method => $args) {
            if (method_exists($query, $method)) {
                $query = call_user_func_array([$query, $method], $args);
            }
        }

        return $query->toArray();
    }

    /**
     * {@inheritdoc}
     *
     * @param array $config Strategy's configuration.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $config = $this->getConfig();
        $assocName = Inflector::pluralize(Inflector::classify($this->_alias));

        $this->_table->belongsTo($assocName, [
            'className' => $this->modelClass,
            'foreignKey' => $config['field'],
            'bindingKey' => 'name',
            'conditions' => [$assocName . '.prefix' => $config['prefix']],
        ]);
    }
}
