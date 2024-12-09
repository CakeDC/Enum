<?php
declare(strict_types=1);

/**
 * Copyright 2015 - 2024, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2024, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Model\Behavior;

use ArrayObject;
use BadMethodCallException;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use CakeDC\Enum\Model\Behavior\Exception\MissingEnumConfigurationException;
use CakeDC\Enum\Model\Behavior\Exception\MissingEnumStrategyException;
use CakeDC\Enum\Model\Behavior\Strategy\ConfigStrategy;
use CakeDC\Enum\Model\Behavior\Strategy\ConstStrategy;
use CakeDC\Enum\Model\Behavior\Strategy\LookupStrategy;
use CakeDC\Enum\Model\Behavior\Strategy\StrategyInterface;
use function Cake\I18n\__d;

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
     * - `nested`: (bool) If `true` the array returned by enum() method will be of form
     *   `[['value' => 'v1', 'text' => 't1'], ['value' => 'v2', 'text' => 't2']`
     *   instead of default `['v1' => 't1', 'v2' => 't2']`.
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
     *           // Create application rule to ensure only valid enum value can be saved.
     *           'applicationRules' => true,
     *           // Allow saving field without any enum value.
     *           'allowEmpty' => false
     *       ],
     *   ];
     *   ```
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
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
    protected array $classMap = [
        'lookup' => LookupStrategy::class,
        'const' => ConstStrategy::class,
        'config' => ConfigStrategy::class,
    ];

    /**
     * Stack of strategies in use.
     *
     * @var array
     */
    protected array $strategies = [];

    /**
     * Initializes the behavior.
     *
     * @param array $config Strategy's configuration.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->normalizeConfig();
    }

    /**
     * Getter/setter for strategies.
     *
     * @param string $alias Strategy's alias.
     * @param mixed $strategy Strategy name from the class map or some strategy instance.
     * @return \CakeDC\Enum\Model\Behavior\Strategy\StrategyInterface
     * @throws \CakeDC\Enum\Model\Behavior\Exception\MissingEnumStrategyException
     */
    public function strategy(string $alias, mixed $strategy): StrategyInterface
    {
        if (!empty($this->strategies[$alias])) {
            return $this->strategies[$alias];
        }

        $this->strategies[$alias] = $strategy;

        if ($strategy instanceof StrategyInterface) {
            return $strategy;
        }

        $class = null;
        if (isset($this->classMap[$strategy])) {
            $class = $this->classMap[$strategy];
        }

        if ($class === null || !class_exists($class)) {
            throw new MissingEnumStrategyException([$class]);
        }

        /** @var \CakeDC\Enum\Model\Behavior\Strategy\StrategyInterface $strategy */
        $strategy = new $class($alias, $this->_table);

        return $this->strategies[$alias] = $strategy;
    }

    /**
     * Normalizes the strategies configuration and initializes the strategies.
     *
     * @return void
     */
    protected function normalizeConfig(): void
    {
        $classMap = $this->getConfig('classMap');
        $this->classMap = array_merge($this->classMap, $classMap);

        $lists = $this->getConfig('lists');
        $defaultStrategy = $this->getConfig('defaultStrategy');

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

            $strategy = $this->strategy($alias, $config['strategy']);
            $strategy->initialize($config);
            $lists[$alias] = $strategy->getConfig();
        }

        $this->setConfig('lists', $lists, false);
    }

    /**
     * @param array|string|null $alias Defined list's alias/name.
     * @return array
     * @throws \CakeDC\Enum\Model\Behavior\Exception\MissingEnumConfigurationException
     */
    public function enum(array|string|null $alias = null): array
    {
        if (is_string($alias)) {
            $config = $this->getConfig('lists.' . $alias);
            if (empty($config)) {
                throw new MissingEnumConfigurationException([$alias]);
            }

            return $this->enumList($alias, $config);
        }

        $lists = $this->getConfig('lists');
        if (!empty($alias)) {
            $lists = array_intersect_key($lists, array_flip($alias));
        }

        $return = [];
        foreach ($lists as $alias => $config) {
            $return[$alias] = $this->enumList($alias, $config);
        }

        return $return;
    }

    /**
     * @param string $alias List alias.
     * @param array $config Config
     * @return array
     */
    protected function enumList(string $alias, array $config): array
    {
        $return = $this->strategy($alias, $config['strategy'])->enum($config);
        if ($this->getConfig('translate')) {
            $return = $this->translate($return);
        }

        if ($this->getConfig('nested')) {
            array_walk(
                $return,
                function (mixed &$item, mixed $val): void {
                    $item = ['value' => $val, 'text' => $item];
                }
            );

            $return = array_values($return);
        }

        return $return;
    }

    /**
     * Translate list values.
     *
     * @param array $list List.
     * @return array
     */
    protected function translate(array $list): array
    {
        $domain = $this->getConfig('translationDomain');

        return array_map(fn ($value) => __d($domain, $value), $list);
    }

    /**
     * @param \Cake\Event\EventInterface $event Event.
     * @param \Cake\ORM\RulesChecker $rules Rules checker.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(EventInterface $event, RulesChecker $rules): RulesChecker
    {
        foreach ($this->getConfig('lists') as $alias => $config) {
            if (Hash::get($config, 'applicationRules') === false) {
                continue;
            }

            $ruleName = 'isValid' . Inflector::camelize($alias);
            $rules->add([$this, $ruleName], $ruleName, [
                'errorField' => $config['field'],
                'message' => $config['errorMessage'],
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
    public function __call(string $method, array $args): bool
    {
        if (!str_starts_with($method, 'isValid')) {
            throw new BadMethodCallException(sprintf('Call to undefined method (%s)', $method));
        }

        $alias = Inflector::underscore(str_replace('isValid', '', $method));
        [$entity, ] = $args;

        $config = $this->getConfig('lists.' . $alias);
        if ($config === null) {
            throw new MissingEnumConfigurationException([$alias]);
        }

        if ($entity->isEmpty($config['field']) && Hash::get($config, 'allowEmpty') === true) {
            return true;
        }

        return array_key_exists($entity->{$config['field']}, $this->enum($alias));
    }

    /**
     * @param \Cake\Event\EventInterface $event The beforeFind event that was fired.
     * @param \Cake\ORM\Query\SelectQuery $query Query
     * @param \ArrayObject $options The options for the query
     * @return void
     */
    public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options): void
    {
        foreach ($this->getConfig('lists') as $alias => $config) {
            $strategy = $this->strategy($alias, $config['strategy']);
            if (method_exists($strategy, 'beforeFind') && $strategy->getConfig('callBeforeFind')) {
                $strategy->beforeFind($event, $query, $options);
            }
        }
    }
}
