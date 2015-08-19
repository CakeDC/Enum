<?php
namespace Enum\Model\Behavior\Strategy;

interface StrategyInterface
{

    /**
     * @param string $prefix Prefix.
     * @return bool
     */
    public function hasPrefix($prefix);

    /**
     * @return array
     */
    public function listPrefixes();

    /**
     * @param array $config
     * @return array
     */
    public function enum(array $config = []);

    /**
     * @param string $group Group name.
     * @param array $config Configuration.
     * @return \Enum\Model\Behavior\Strategy\StrategyInterface
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
