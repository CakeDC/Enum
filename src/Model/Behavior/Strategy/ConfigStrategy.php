<?php
namespace Enum\Model\Behavior\Strategy;

use Cake\Core\Configure;

class ConfigStrategy extends AbstractStrategy
{
    public function listPrefixes()
    {
        if (!$lists = Configure::read('Enum')) {
            throw new \RuntimeException();
        }

        $prefixes = array_keys($lists);
        return array_map('strtoupper', $prefixes);
    }

    public function enum(array $config = [])
    {
        if (!$list = Configure::read('Enum.' . strtolower($this->config('prefix')))) {
            throw new \RuntimeException();
        }

        return $list;
    }

    public function initialize($config)
    {
        parent::initialize($config);
        $enumConfig = Configure::read('Enum');

        foreach ($enumConfig as $prefix => $enumOpts) {
            unset($enumConfig[$prefix]);
            $enumConfig[strtolower($prefix)] = $enumOpts;
        }

        Configure::write('Enum', $enumConfig);
        return $this;
    }
}