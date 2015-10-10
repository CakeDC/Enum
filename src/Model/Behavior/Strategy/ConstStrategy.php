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

use Cake\ORM\Table;
use ReflectionClass;

class ConstStrategy extends AbstractStrategy
{

    /**
     * Constants list
     *
     * @var array
     */
    protected $_constants;

    /**
     * {@inheritdoc}
     *
     * @param string $alias Strategy's alias.
     * @param \Cake\ORM\Table $table Table object.
     */
    public function __construct($alias, Table $table)
    {
        parent::__construct($alias, $table);
        $this->_defaultConfig['prefix'] = strtoupper($alias);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $config List of callable filters to limit items generated from list.
     * @return array
     */
    public function enum(array $config = [])
    {
        $constants = $this->_getConstants();
        $keys = array_keys($constants);

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
        if (isset($this->_constants)) {
            return $constants;
        }

        $constants = (new ReflectionClass(get_class($this->_table)))->getConstants();
        $constantsKeys = array_keys($constants);

        $prefix = $this->config('prefix');
        $keys = array_filter($constantsKeys, function ($v) use ($prefix) {
            return strpos($v, $prefix) === 0;
        });

        $this->_constants = array_intersect_key($constants, array_flip($keys));
        return $this->_constants;
    }
}
