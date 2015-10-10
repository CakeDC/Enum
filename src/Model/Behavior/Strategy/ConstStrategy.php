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

use ReflectionClass;

class ConstStrategy extends AbstractStrategy
{

    /**
     * {@inheritdoc}
     *
     * @param array $config List of callable filters to limit items generated from list.
     * @return array
     */
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

    /**
     * Returns defined constants for the current `$_table`.
     *
     * @return array
     */
    protected function _getConstants()
    {
        return (new ReflectionClass(get_class($this->_table)))->getConstants();
    }
}
