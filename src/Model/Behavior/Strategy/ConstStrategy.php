<?php
namespace Enum\Model\Behavior\Strategy;

class ConstStrategy extends AbstractStrategy
{
    public function listPrefixes()
    {
        $constants = array_keys($this->_getConstants());
        $matrix = [];

        foreach ($constants as $constant) {
            $parts = explode('_', $constant);
            foreach ($parts as $part) {
                $matrix += [$part => 0];
                $matrix[$part]++;
            }
        }

        unset($matrix['VALIDATOR']); // one of cake's own constants.

        return  array_keys(array_filter($matrix, function ($v) {
            return $v >= 2;
        }));
    }

    public function enum(array $config = [])
    {
        $prefix = $this->config('prefix');
        $constants = $this->_getConstants();
        $constantsKeys = array_keys($constants);

        $keys = array_filter($constantsKeys, function ($v) use ($prefix) {
            return strpos($v, $prefix) === 0;
        });

        foreach ($config as $callable) {
            if (is_callable($callable)) {
                $keys = array_filter($keys, $callable);
            }
        }

        $values = array_map(function ($v) use ($constants) {
            return $constants[$v];
        }, $keys);

        return array_combine($keys, $values);
    }

    protected function _getConstants()
    {
        return (new \ReflectionClass(get_class($this->_table)))->getConstants();
    }
}