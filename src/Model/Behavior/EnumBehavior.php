<?php

/**
 * Copyright 2015, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Model\Behavior;

use BadMethodCallException;
use CakeDC\Enum\Model\Behavior\Exception\MissingEnumConfigurationException;
use CakeDC\Enum\Model\Behavior\Exception\MissingEnumStrategyException;
use CakeDC\Enum\Model\Behavior\Strategy\AbstractStrategy;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\RulesChecker;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

class EnumBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * - `defaultStrategy`: the default strategy to use.
     * - `translate`: Whether values of lists returned by enum() method should
     *   be translated. Defaults to `false`.
     * - `translationDomain`: Domain to use when translating list value.
     *   Defaults to "default".
     * - `implementedMethods`: custom table methods made accessible by this behavior.
     * - `lists`: the defined enumeration lists. Lists can use different strategies,
     *   use prefixes to differentiate them (defaults to the uppercased list name) and
     *   are persisted into a table's field (default to the underscored list name).
     *
     *   Example:
     *
     *   ```php
     *   $lists = [
     *       'priority' => [
     *           'strategy' => 'lookup',
     *           'prefix' => 'PRIORITY',
     *           'field' => 'priority',
     *           'errorMessage' => 'Invalid priority',
     *           'applicationRules' => true
     *       ],
     *   ];
     *   ```
     *
     * @var array
     */
    protected $_defaultConfig = [
        'defaultStrategy' => 'lookup',
        'translate' => false,
        'translationDomain' => 'default',
        'implementedMethods' => [
            'enum' => 'enum',
        ],
        'classMap' => [],
        'lists' => [],
    ];

    /**
     * Class map.
     *
     * @var array
     */
    protected $_classMap = [
        'lookup' => 'CakeDC\Enum\Model\Behavior\Strategy\LookupStrategy',
        'const' => 'CakeDC\Enum\Model\Behavior\Strategy\ConstStrategy',
        'config' => 'CakeDC\Enum\Model\Behavior\Strategy\ConfigStrategy',
    ];

    /**
     * Stack of strategies in use.
     *
     * @var array
     */
    protected $_strategies = [];

    /**
     * Initializes the behavior.
     *
     * @param array $config Strategy's configuration.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_normalizeConfig();
    }

    /**
     * Getter/setter for strategies.
     *
     * @param string $alias Strategy's alias.
     * @param mixed $strategy Strategy name from the class map or some strategy instance.
     * @return \CakeDC\Enum\Model\Behavior\Strategy\StrategyInterface
     * @throws \CakeDC\Enum\Model\Behavior\Exception\MissingEnumStrategyException
     */
    public function strategy($alias, $strategy)
    {
        if (!empty($this->_strategies[$alias])) {
            return $this->_strategies[$alias];
        }

        $this->_strategies[$alias] = $strategy;

        if ($strategy instanceof AbstractStrategy) {
            return $strategy;
        }

        if (isset($this->_classMap[$strategy])) {
            $class = $this->_classMap[$strategy];
        }

        if (!class_exists($class)) {
            throw new MissingEnumStrategyException([$class]);
        }

        return $this->_strategies[$alias] = new $class($alias, $this->_table);
    }

    /**
     * Normalizes the strategies configuration and initializes the strategies.
     *
     * @return void
     */
    protected function _normalizeConfig()
    {
        $classMap = $this->config('classMap');
        $this->_classMap = array_merge($this->_classMap, $classMap);

        $lists = $this->config('lists');
        $defaultStrategy = $this->config('defaultStrategy');

        foreach ($lists as $alias => $config) {
            if (is_numeric($alias)) {
                unset($lists[$alias]);
                $alias = $config;
                $config = [];
                $lists[$alias] = $config;
            }

            if (is_string($config)) {
                $config = ['prefix' => strtoupper($config)];
            }

            if (empty($config['strategy'])) {
                $config['strategy'] = $defaultStrategy;
            }

            $lists[$alias] = $this->strategy($alias, $config['strategy'])
                ->initialize($config)
                ->config();
        }

        $this->config('lists', $lists, false);
    }

    /**
     * @param string|array|null $alias Defined list's alias/name.
     * @return array
     * @throws \CakeDC\Enum\Model\Behavior\Exception\MissingEnumConfigurationException
     */
    public function enum($alias = null)
    {
        if (is_string($alias)) {
            $config = $this->config('lists.' . $alias);
            if (empty($config)) {
                throw new MissingEnumConfigurationException([$alias]);
            }

            $return = $this->strategy($alias, $config['strategy'])->enum($config);
            if ($this->config('translate')) {
                $return = $this->_translate($return);
            }

            return $return;
        }

        $lists = $this->config('lists');
        if (!empty($alias)) {
            $lists = array_intersect_key($lists, array_flip($alias));
        }

        $return = [];
        foreach ($lists as $alias => $config) {
            $return[$alias] = $this->strategy($alias, $config['strategy'])->enum($config);
            if ($this->config('translate')) {
                $return[$alias] = $this->_translate($return[$alias]);
            }
        }

        return $return;
    }

    /**
     * Translate list values.
     *
     * @param array $list List.
     * @return array
     */
    protected function _translate(array $list)
    {
        $domain = $this->config('translationDomain');

        return array_map(function ($value) use ($domain) {
            return __d($domain, $value);
        }, $list);
    }

    /**
     * @param \Cake\Event\Event $event Event.
     * @param \Cake\ORM\RulesChecker $rules Rules checker.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(Event $event, RulesChecker $rules)
    {
        foreach ($this->config('lists') as $alias => $config) {
            if (Hash::get($config, 'applicationRules') === false) {
                continue;
            }

            $ruleName = 'isValid' . Inflector::camelize($alias);
            $rules->add([$this, $ruleName], $ruleName, [
                'errorField' => $config['field'],
                'message' => $config['errorMessage']
            ]);
        }

        return $rules;
    }

    /**
     * Universal validation rule for lists.
     *
     * @param string $method Method name.
     * @param array $args Method's arguments.
     * @return bool
     * @throws \BadMethodCallException
     * @throws \CakeDC\Enum\Model\Behavior\Exception\MissingEnumConfigurationException
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'isValid') !== 0) {
            throw new BadMethodCallException(sprintf('Call to undefined method (%s)', $method));
        }

        $alias = Inflector::underscore(str_replace('isValid', '', $method));
        list($entity, ) = $args;

        if (!$config = $this->config('lists.' . $alias)) {
            throw new MissingEnumConfigurationException([$alias]);
        }

        return array_key_exists($entity->{$config['field']}, $this->enum($alias));
    }
}
