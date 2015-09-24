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

namespace CakeDC\Enum\Model\Table;

use Cake\ORM\Table;

class LookupsTable extends Table
{

    /**
     * {@inheritdoc}
     *
     * @param array $config Table's configuration.
     */
    public function initialize(array $config)
    {
        $this->table('enum_lookups');
        $this->displayField('label');
    }
}
