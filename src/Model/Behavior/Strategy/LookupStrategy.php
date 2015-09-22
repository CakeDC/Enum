<?php

/**
 * Copyright 2010 - 2015, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2015, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Model\Behavior\Strategy;

use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class LookupStrategy extends AbstractStrategy
{

    use ModelAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct($alias, Table $table)
    {
        parent::__construct($alias, $table);
        $this->_defaultConfig['prefix'] = strtoupper($alias);
        $this->modelClass = 'CakeDC/Enum.Lookups';
        $this->modelFactory('Table', ['Cake\ORM\TableRegistry', 'get']);
    }

    /**
     * {@inheritdoc}
     */
    public function listPrefixes()
    {
        if (empty($this->_prefixes)) {
            $this->_prefixes = array_keys($this->loadModel()->find('list', [
                'keyField' => 'prefix',
            ])->toArray());
        }

        return $this->_prefixes;
    }

    /**
     * {@inheritdoc}
     */
    public function enum(array $config = [])
    {
        $query = $this->loadModel()
            ->find('list', [
                'keyField' => 'group',
                'valueField' => 'label',
            ])
            ->where([
                'prefix' => $this->config('prefix'),
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
     */
    public function initialize($config)
    {
        $config = parent::initialize($config)->config();
        $assocName = Inflector::pluralize(Inflector::classify($this->_alias));

        $this->_table->belongsTo($assocName, [
            'className' => $this->modelClass,
            'foreignKey' => $config['field'],
            'bindingKey' => 'name',
            'conditions' => [$assocName . '.prefix' => $config['prefix']],
        ]);

        return $this;
    }
}
