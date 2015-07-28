<?php
namespace Enum\Model\Entity;

use Cake\ORM\Entity;

class Lookup extends Entity
{
    protected function _getGroup()
    {
        return implode('_', [
            $this->prefix,
            $this->name,
        ]);
    }
}