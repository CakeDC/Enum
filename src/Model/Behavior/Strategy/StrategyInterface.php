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

namespace CakeDC\Enum\Model\Behavior\Strategy;

interface StrategyInterface
{

    /**
     * @param mixed $config Strategy's configuration.
     * @return array
     */
    public function enum(array $config = []);

    /**
     * @param array $config Configuration.
     * @return \CakeDC\Enum\Model\Behavior\Strategy\StrategyInterface
     */
    public function initialize($config);

    /**
     * @param string|array|null $key The key to get/set, or a complete array of configs.
     * @param mixed|null $value The value to set.
     * @param bool $merge Whether to recursively merge or overwrite existing config, defaults to true.
     * @return mixed Config value being read, or the object itself on write operations.
     * @throws \Cake\Core\Exception\Exception When trying to set a key that is invalid.
     */
    public function config($key = null, $value = null, $merge = true);
}
