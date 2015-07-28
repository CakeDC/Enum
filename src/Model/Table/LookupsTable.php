<?php
namespace Enum\Model\Table;

use Cake\ORM\Table;

class LookupsTable extends Table
{

    public function initialize(array $config)
    {
        $this->table('enum_lookups');
        $this->displayField('label');
    }
}