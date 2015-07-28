<?php
namespace Enum\Model\Behavior\Strategy;

use Cake\Core\InstanceConfigTrait;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class LookupStrategy extends AbstractStrategy
{
    protected $_defaultConfig = [
        'table' => 'Enum.Lookups',
    ];

    protected $_lookups;

    protected function _lookups()
    {
        if (empty($this->_lookups)) {
            $this->_lookups = TableRegistry::get($this->config('table'));
        }

        return $this->_lookups;
    }

    public function hasPrefix($prefix)
    {
        return in_array(strtoupper($prefix), $this->listPrefixes());
    }

    public function listPrefixes()
    {
        if (empty($this->_prefixes)) {
            $this->_prefixes = array_keys($this->_lookups()->find('list', [
                'keyField' => 'prefix',
            ])->toArray());
        }

        return $this->_prefixes;
    }
}