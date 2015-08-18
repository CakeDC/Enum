<?php
namespace Enum\Model\Behavior\Strategy;

use Cake\Core\InstanceConfigTrait;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use RuntimeException;

abstract class AbstractStrategy
{

    use InstanceConfigTrait;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * List of defined group prefixes.
     *
     * @var array
     */
    protected $_prefixes = [];

    /**
     * Target table.
     *
     * @var \Cake\ORM\Table
     */
    protected $_table;

    /**
     * @param array $config Configuration.
     * @param \Cake\ORM\Table $table Target table.
     */
    public function __construct($alias, Table $table)
    {
        $this->_alias = $alias;
        $this->_table = $table;
    }

    /**
     * @param string $prefix Prefix.
     * @return bool
     */
    abstract public function hasPrefix($prefix);

    /**
     * @return array
     */
    abstract public function listPrefixes();

    /**
     * @param array $config
     * @return array
     */
    abstract public function enum(array $config);

    /**
     * @param string $key
     * @return string
     */
    abstract public function get($key);

    /**
     * @param string $group Group name.
     * @param array $config Configuration.
     * @return array
     * @throws \RuntimeException if group's prefix is not defined.
     */
    public function normalize($config)
    {
        if (is_string($config)) {
            $config = ['prefix' => $config];
        }

        if (empty($config['prefix'])) {
            $prefix = Inflector::underscore(Inflector::singularize($this->_table->alias()));
            $prefix .= '_' . $this->_alias;
            if (!$this->hasPrefix($prefix)) {
                if (!$this->hasPrefix($this->_alias)) {
                    throw new RuntimeException(sprintf('Undefined prefix for strategy (%s)', $this->_alias));
                }
                $prefix = $this->_alias;
            }
            $config += ['prefix' => strtoupper($prefix)];
        }

        if (empty($config['field'])) {
            $config['field'] = Inflector::underscore(Inflector::singularize($this->_alias));
        }

        if (empty($config['errorMessage'])) {
            $config['errorMessage'] = __d('cake', 'The provided value is invalid');
        }

        $this->config($config);
        return $config;
    }
}
