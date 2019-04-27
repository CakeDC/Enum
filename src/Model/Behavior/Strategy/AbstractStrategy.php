<?php

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

use CakeDC\Enum\Model\Behavior\Exception\MissingEnumStrategyPrefixException;
use Cake\Core\InstanceConfigTrait;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

abstract class AbstractStrategy implements StrategyInterface
{

    use InstanceConfigTrait;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Target table.
     *
     * @var \Cake\ORM\Table
     */
    protected $_table;

    /**
     * Table alias.
     *
     * @var string
     */
    protected $_alias;

    /**
     * Constructor.
     *
     * @param string $alias Alias assigned to the strategy in the behavior.
     * @param \Cake\ORM\Table $table Target table.
     */
    public function __construct($alias, Table $table)
    {
        $this->_alias = $alias;
        $this->_table = $table;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function initialize(array $config)
    {
        $prefix = $this->getConfig('prefix');
        if (empty($config['prefix']) && empty($prefix)) {
            $config['prefix'] = $this->_generatePrefix();
        }

        if (empty($config['field'])) {
            $config['field'] = Inflector::underscore($this->_alias);
        }

        if (empty($config['errorMessage'])) {
            $config['errorMessage'] = __d('cake', 'The provided value is invalid');
        }

        $this->setConfig($config);

        return $this;
    }

    /**
     * Generates default prefix for strategy.
     *
     * @return string
     */
    protected function _generatePrefix()
    {
        $prefix = Inflector::underscore(Inflector::singularize($this->_table->getAlias()));
        $prefix .= '_' . $this->_alias;

        return strtoupper($prefix);
    }
}
