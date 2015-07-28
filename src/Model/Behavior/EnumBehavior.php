<?php
namespace Enum\Model\Behavior;

use Cake\ORM\Behavior;
use Enum\Model\Behavior\Strategy\AbstractStrategy;
use RuntimeException;

class EnumBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'defaultStrategy' => 'lookup',
        'implementedMethods' => [
            'enum' => 'enum',
        ],
        'providers' => [],
    ];

    /**
     * Class map.
     *
     * @var array
     */
    protected $_classMap = [
        'lookup' => 'Enum\Model\Behavior\Strategy\LookupStrategy',
        'const' => 'Enum\Model\Behavior\Strategy\ConstStrategy',
        'enum' => 'Enum\Model\Behavior\Strategy\EnumStrategy',
    ];

    protected $_strategies = [];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_normalizeConfig();
    }

    /**
     * @return \Enum\Model\Behavior\Strategy\AbstractStrategy
     */
    public function strategy($alias, $strategy)
    {
        if (!empty($this->_strategies[$alias])) {
            return $this->_strategies[$alias];
        }

        $this->_strategies[$alias] = $strategy;
        if (!($strategy instanceof AbstractStrategy)) {
            if (isset($this->_classMap[$strategy])) {
                $strategy = $this->_classMap[$strategy];
            }

            if (!class_exists($strategy)) {
                throw new RuntimeException();
            }

            $this->_strategies[$alias] = new $strategy($alias, $this->_table);
        }

        return $this->_strategies[$alias];
    }

    protected function _normalizeConfig()
    {
        $providers = $this->config('providers');
        $strategy = $this->config('defaultStrategy');

        foreach ($providers as $alias => $options) {
            if (is_numeric($alias)) {
                unset($providers[$alias]);
                $alias = $options;
                $options = [];
                $providers[$alias] = $options;
            }

            if (is_string($options)) {
                $options = ['prefix' => strtoupper($options)];
            }

            if (empty($options['strategy'])) {
                $options['strategy'] = $strategy;
            }

           $providers[$alias] =  $this->strategy($alias, $options['strategy'])
               ->initialize($options);
        }

        $this->config('providers', $providers, false);
    }

    /**
     * @param string $group Defined group name.
     * @return array
     */
    public function enum($group)
    {
        $config = $this->config('providers.' . $group);
        if (empty($config)) {
            throw new RuntimeException();
        }

        return $this->strategy($group, $config['strategy'])->enum($config);
    }
}