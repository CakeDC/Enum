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
        ]
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

    protected $_strategy = [];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_normalizeConfig();
    }

    /**
     * @return \Enum\Model\Behavior\Strategy\AbstractStrategy
     */
    public function strategy($group, $strategy)
    {
        if (!empty($this->_strategy[$group])) {
            return $this->_strategy[$group];
        }

        $this->_strategy[$group] = $strategy;
        if (!($strategy instanceof AbstractStrategy)) {
            if (isset($this->_classMap[$strategy])) {
                $strategy = $this->_classMap[$strategy];
            }

            if (!class_exists($strategy)) {
                throw new RuntimeException();
            }

            $this->_strategy[$group] = new $strategy($this->config(), $this->_table);
        }

        return $this->_strategy[$group];
    }

    protected function _normalizeConfig()
    {
        $groups = $this->config('groups');
        $strategy = $this->config('defaultStrategy');

        foreach ($groups as $group => $options) {
            if (is_numeric($group)) {
                unset($groups[$group]);
                $group = $options;
                $options = [];
                $groups[$group] = $options;
            }

            if (is_string($options)) {
                $options = ['prefix' => strtoupper($options)];
            }

            if (empty($options['strategy'])) {
                $options['strategy'] = $strategy;
            }

           $groups[$group] =  $this->strategy($group, $options['strategy'])
               ->normalizeGroupConfig($group, $options);
        }

        $this->config('groups', $groups, false);
    }

    /**
     * @param string $group Defined group name.
     * @return array
     */
    public function enum($group)
    {
        $config = $this->config('groups.' . $group);
        if (empty($config)) {
            throw new RuntimeException();
        }

        return $this->strategy($group, $config['strategy'])->enum($config);
    }
}