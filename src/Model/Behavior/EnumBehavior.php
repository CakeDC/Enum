<?php
namespace Enum\Model\Behavior;

use Cake\ORM\Behavior;

class EnumBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'strategy' => 'lookup',
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

    protected $_strategy;

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_normalizeConfig();
    }

    /**
     * @return \Enum\Model\Behavior\Strategy\AbstractStrategy
     */
    public function strategy()
    {
        if (empty($this->_strategy)) {
            $this->_strategy = $this->_getStrategy($this->config('strategy'));
        }

        return $this->_strategy;
    }

    /**
     * @param \Enum\Model\Behavior\Strategy\AbstractStrategy|string $strategy Strategy.
     * @return \Enum\Model\Behavior\Strategy\AbstractStrategy
     */
    protected function _getStrategy($strategy)
    {
        $instance = $strategy;
        if (!($strategy instanceof AbstractEnumStrategy)) {
            if (isset($this->_classMap[$strategy])) {
                $strategy = $this->_classMap[$strategy];
            }
            $instance = new $strategy($this->config(), $this->_table);
        }

        return $instance;
    }

    protected function _normalizeConfig()
    {
        $config = $this->strategy()->normalizeConfig($this->config());
        $this->config($config, null, false);
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

        return $this->strategy()->enum($config);
    }
}