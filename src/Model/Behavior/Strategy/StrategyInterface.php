<?php
declare(strict_types=1);

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

interface StrategyInterface
{
    /**
     * @param array $config Strategy's configuration.
     * @return array
     */
    public function enum(array $config = []): array;

    /**
     * @param array $config Configuration.
     * @return void
     */
    public function initialize(array $config): void;

    /**
     * Returns the config.
     *
     * ### Usage
     *
     * Reading the whole config:
     *
     * ```
     * $this->getConfig();
     * ```
     *
     * Reading a specific value:
     *
     * ```
     * $this->getConfig('key');
     * ```
     *
     * Reading a nested value:
     *
     * ```
     * $this->getConfig('some.nested.key');
     * ```
     *
     * Reading with default value:
     *
     * ```
     * $this->getConfig('some-key', 'default-value');
     * ```
     *
     * @param string|null $key The key to get or null for the whole config.
     * @param mixed $default The return value when the key does not exist.
     * @return mixed Config value being read.
     */
    public function getConfig(?string $key = null, $default = null);
}
