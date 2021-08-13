<?php
declare(strict_types=1);

/**
 * Copyright 2015 - 2019, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2019, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Model\Behavior\Exception;

use \Cake\Core\Exception\CakeException;

class InvalidAliasListException extends CakeException
{
    protected $_messageTemplate = 'Invalid alias %s. %s defined association %s.';
}
