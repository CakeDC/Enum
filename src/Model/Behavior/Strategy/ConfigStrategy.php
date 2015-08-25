<?php
namespace CakeDC\Enum\Model\Behavior\Strategy;

use Cake\Core\Configure;

class ConfigStrategy extends AbstractStrategy
{
    const KEY = 'CakeDC/Enum';

    /**
     * {@inheritdoc}
     */
    public function listPrefixes()
    {
        if (!$lists = Configure::read(self::KEY)) {
            return [];
        }

        $prefixes = array_keys($lists);
        return array_map('strtoupper', $prefixes);
    }

    /**
     * {@inheritdoc}
     */
    public function enum(array $config = [])
    {
        if (!$list = Configure::read(self::KEY . '.' . strtolower($this->config('prefix')))) {
            return [];
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($config)
    {
        parent::initialize($config);
        $enumConfig = Configure::read(self::KEY);

        foreach ($enumConfig as $prefix => $enumOpts) {
            unset($enumConfig[$prefix]);
            $enumConfig[strtolower($prefix)] = $enumOpts;
        }

        Configure::write(self::KEY, $enumConfig);
        return $this;
    }
}
