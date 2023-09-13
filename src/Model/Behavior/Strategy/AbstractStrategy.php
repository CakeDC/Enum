<?php
declare(strict_types=1);

/**
 * Copyright 2015 - 2023, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2023, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Model\Behavior\Strategy;

use Cake\Core\InstanceConfigTrait;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use function Cake\I18n\__d;

abstract class AbstractStrategy implements StrategyInterface
{
    use InstanceConfigTrait;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected array $_defaultConfig = [];

    /**
     * Target table.
     *
     * @var \Cake\ORM\Table
     */
    protected Table $table;

    /**
     * Table alias.
     *
     * @var string
     */
    protected string $alias;

    /**
     * Constructor.
     *
     * @param string $alias Alias assigned to the strategy in the behavior.
     * @param \Cake\ORM\Table $table Target table.
     */
    public function __construct(string $alias, Table $table)
    {
        $this->alias = $alias;
        $this->table = $table;
    }

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        $prefix = $this->getConfig('prefix');
        if (empty($config['prefix']) && empty($prefix)) {
            $config['prefix'] = $this->generatePrefix();
        }

        if (empty($config['field'])) {
            $config['field'] = Inflector::underscore($this->alias);
        }

        if (empty($config['errorMessage'])) {
            $config['errorMessage'] = __d('cake', 'The provided value is invalid');
        }
        if (!isset($config['callBeforeFind'])) {
            $config['callBeforeFind'] = true;
        }

        $this->setConfig($config);
    }

    /**
     * Generates default prefix for strategy.
     *
     * @return string
     */
    protected function generatePrefix(): string
    {
        $prefix = Inflector::underscore(Inflector::singularize($this->table->getAlias()));
        $prefix .= '_' . $this->alias;

        return strtoupper($prefix);
    }
}
