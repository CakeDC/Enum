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

use Cake\Core\Exception\Exception;

class MissingEnumStrategyPrefixException extends Exception
{
    protected $_templateMessage = 'Missing prefix for strategy (%s)';
}
