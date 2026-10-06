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
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\Validation\Validator;
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
     * - `validation`: Default value for the lists' `validation` option.
     *   Defaults to `false`.
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
     *           // Supports `:value` (given value) and `:expected` (valid values) placeholders.
     *           'errorMessage' => 'Invalid priority',
     *           // Create application rule to ensure only valid enum value can be saved.
     *           'applicationRules' => true,
     *           // Allow saving field without any enum value.
     *           'allowEmpty' => false,
     *           // Add a validation rule to the given validators. `true` means
     *           // `['default']`, a string or an array names the validators.
     *           'validation' => false,
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
        'validation' => false,
        'implementedMethods' => [
            'enum' => 'enum',
        ],
        'classMap' => [],
        'lists' => [],
    ];

    /**
     * Class map.
     *
     * @var array<string, class-string<\CakeDC\Enum\Model\Behavior\Strategy\StrategyInterface>>
     */
    protected array $classMap = [
        'lookup' => LookupStrategy::class,
        'const' => ConstStrategy::class,
        'config' => ConfigStrategy::class,
    ];

    /**
     * Stack of strategies in use.
     *
     * @var array<string, \CakeDC\Enum\Model\Behavior\Strategy\StrategyInterface>
     */
    protected array $strategies = [];

    /**
     * Initializes the behavior.
     *
     * @param array<string, mixed> $config Strategy's configuration.
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
     * @param array<int, string>|string|null $alias Defined list's alias/name.
     * @return array<string, mixed>
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
     * @param array<string, mixed> $config Config
     * @return array<string, mixed>
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
                },
            );

            $return = array_values($return);
        }

        return $return;
    }

    /**
     * Translate list values.
     *
     * @param array<string, mixed> $list List.
     * @return array<string, mixed>
     */
    protected function translate(array $list): array
    {
        $domain = $this->getConfig('translationDomain');

        return array_map(fn($value) => __d($domain, $value), $list);
    }

    /**
     * Build the rules for enumeration lists with activated application rules
     *
     * @param \Cake\Event\EventInterface<\Cake\ORM\Table> $event Event.
     * @param \Cake\ORM\RulesChecker $rules The RulesChecker to ammend.
     * @return void
     */
    public function buildRules(EventInterface $event, RulesChecker $rules): void
    {
        foreach ($this->getConfig('lists') as $alias => $config) {
            if (Hash::get($config, 'applicationRules') === false) {
                continue;
            }

            $ruleName = 'isValid' . Inflector::camelize($alias);
            $rules->add([$this, $ruleName], $ruleName, [
                'errorField' => $config['field'],
                'message' => fn(EntityInterface $entity): string => $this->errorMessage(
                    $alias,
                    $entity->get($config['field']),
                ),
            ]);
        }

        $event->setResult($rules);
    }

    /**
     * Adds a validation rule to the validator for each list enabling it.
     *
     * @param \Cake\Event\EventInterface<\Cake\ORM\Table> $event Event.
     * @param \Cake\Validation\Validator $validator Validator.
     * @param string $name Validator name.
     * @return void
     */
    public function buildValidator(EventInterface $event, Validator $validator, string $name): void
    {
        foreach ($this->getConfig('lists') as $alias => $config) {
            if (!in_array($name, $this->validatorNames($config), true)) {
                continue;
            }

            $field = $config['field'];
            $validator->add($field, 'isValid' . Inflector::camelize($alias), [
                'rule' => fn(mixed $value): bool|string => $this->isValidValue($alias, $value)
                    ?: $this->errorMessage($alias, $value),
            ]);

            if (Hash::get($config, 'allowEmpty') === true) {
                $validator->allowEmptyString($field);
            }
        }
    }

    /**
     * Returns the names of the validators a list adds its rule to.
     *
     * @param array<string, mixed> $config List configuration.
     * @return array<string>
     */
    protected function validatorNames(array $config): array
    {
        $validation = $config['validation'] ?? $this->getConfig('validation');
        if ($validation === true) {
            return ['default'];
        }

        return $validation ? (array)$validation : [];
    }

    /**
     * Checks whether the value is a key of the list.
     *
     * @param string $alias List alias.
     * @param mixed $value Value to check.
     * @return bool
     */
    protected function isValidValue(string $alias, mixed $value): bool
    {
        $config = $this->getConfig('lists.' . $alias);
        $list = $this->strategy($alias, $config['strategy'])->enum($config);

        return array_key_exists($this->normalizeValue($value), $list);
    }

    /**
     * Universal validation rule for lists.
     *
     * @param string $method Method name.
     * @param array<int, mixed> $args Method's arguments.
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

        if (!$entity->hasValue($config['field']) && Hash::get($config, 'allowEmpty') === true) {
            return true;
        }

        return $this->isValidValue($alias, $entity->get($config['field']));
    }

    /**
     * Builds the validation error message for a list, replacing the `:value`
     * and `:expected` placeholders.
     *
     * @param string $alias List alias.
     * @param mixed $value Value being validated.
     * @return string
     */
    protected function errorMessage(string $alias, mixed $value): string
    {
        $config = $this->getConfig('lists.' . $alias);
        $expected = array_map(
            fn($key): string => "'" . $key . "'",
            array_keys($this->strategy($alias, $config['strategy'])->enum($config)),
        );

        return Text::insert($config['errorMessage'], [
            'value' => (string)$this->normalizeValue($value),
            'expected' => Text::toList($expected, __d('cake', 'or')),
        ]);
    }

    /**
     * Extracts the enum value from a field value.
     *
     * @param mixed $value Field value.
     * @return mixed
     */
    protected function normalizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $value['value'] ?? '';
        }
        if ($value instanceof EntityInterface) {
            return $value->get('value');
        }

        return $value;
    }

    /**
     * @param \Cake\Event\EventInterface<\Cake\ORM\Table> $event The beforeFind event that was fired.
     * @param \Cake\ORM\Query\SelectQuery<\Cake\Datasource\EntityInterface> $query Query
     * @param \ArrayObject<string, mixed> $options The options for the query
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
