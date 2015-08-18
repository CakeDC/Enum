<?php
namespace Enum\Model\Behavior\Strategy;

use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class LookupStrategy extends AbstractStrategy
{

    use ModelAwareTrait;

    /**
     * @inheritdoc
     */
    public function __construct($alias, Table $table)
    {
        parent::__construct($alias, $table);
        $this->modelClass = 'Enum.Lookups';
        $this->modelFactory('Table', ['Cake\ORM\TableRegistry', 'get']);
    }

    /**
     * @inheritdoc
     */
    public function hasPrefix($prefix)
    {
        return in_array(strtoupper($prefix), $this->listPrefixes());
    }

    /**
     * @inheritdoc
     */
    public function listPrefixes()
    {
        if (empty($this->_prefixes)) {
            $this->_prefixes = array_keys($this->loadModel()->find('list', [
                'keyField' => 'prefix',
            ])->toArray());
        }

        return $this->_prefixes;
    }

    /**
     * @inheritdoc
     */
    public function enum(array $config)
    {
        $query = $this->loadModel()
            ->find('list', [
                'keyField' => 'group',
                'valueField' => 'label',
            ])
            ->where([
                'prefix' => $config['prefix'],
            ]);

        foreach ($config as $method => $args) {
            if (method_exists($query, $method)) {
                $query = call_user_func_array([$query, $method], $args);
            }
        }

        return $query->toArray();
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->loadModel()->find()
            ->where(['name' => $key, 'prefix' => $this->config('prefix')]);
    }

    /**
     * @inheritdoc
     */
    public function initialize($config)
    {
        $config = parent::initialize($config);
        $assocName = Inflector::pluralize(Inflector::classify($this->_alias));

        $this->_table->belongsTo($assocName, [
            'className' => $this->modelClass,
            'foreignKey' => $config['field'],
            'bindingKey' => 'name',
            'conditions' => [$assocName . '.prefix' => strtoupper($this->_alias)],
        ]);

        return $config;
    }
}
