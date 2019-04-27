<?php

/**
 * Copyright 2015 - 2018, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2018, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Model\Behavior\Strategy;

interface StrategyInterface
{

    /**
     * @param array $config Strategy's configuration.
     * @return array
     */
    public function enum(array $config = []);

    /**
     * @param array $config Configuration.
     * @return \CakeDC\Enum\Model\Behavior\Strategy\StrategyInterface
     */
    public function initialize(array $config);

    /**
     * @param string|array|null $key The key to get/set, or a complete array of configs.
     * @param mixed|null $default The value to set.
     * @return mixed Config value being read, or the object itself on write operations.
     * @throws \Cake\Core\Exception\Exception When trying to set a key that is invalid.
     */
    public function getConfig($key = null, $default = null);
}
