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
     * List of defined group prefixes.
     *
     * @var array
     */
    protected $_prefixes = [];

    /**
     * Target table.
     *
     * @var |Cake\ORM\Table
     */
    protected $_table;

    /**
     * @param array $config Configuration.
     * @param \Cake\ORM\Table $table Target table.
     */
    public function __construct(array $config, Table $table)
    {
        $this->config($config);
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
     * @param array $config Configuration.
     * @return array
     * @throws \RuntimeException if group's prefix is not defined.
     */
    public function normalizeConfig($config)
    {
        foreach ($config['groups'] as $group => $options) {
            if (is_numeric($group)) {
                unset($config['groups'][$group]);
                $group = $options;
                $config['groups'][$group] = $options = [];
            }

            if (is_string($options)) {
                $options = ['prefix' => strtoupper($options)];
            }

            if (empty($options['prefix'])) {
                $prefix = Inflector::underscore(Inflector::singularize($this->_table->alias()));
                $prefix .= '_' . $group;
                if (!$this->hasPrefix($prefix)) {
                    if (!$this->hasPrefix($group)) {
                        throw new RuntimeException(sprintf('Undefined prefix for group (%s)', $group));
                    }
                    $prefix = $group;
                }
            }

            $options += ['prefix' => strtoupper($prefix)];
            $config['groups'][$group] = $options;
        }

        return $config;
    }
}