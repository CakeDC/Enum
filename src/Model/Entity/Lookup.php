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

namespace CakeDC\Enum\Model\Entity;

use Cake\ORM\Entity;

class Lookup extends Entity
{

    /**
     * Virtual field to get `prefix_name` notation.
     *
     * @return string
     */
    protected function _getGroup()
    {
        return implode('_', [
            $this->prefix,
            $this->name,
        ]);
    }
}
