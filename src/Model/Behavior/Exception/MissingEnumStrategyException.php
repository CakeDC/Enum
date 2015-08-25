<?php
namespace CakeDC\Enum\Model\Behavior\Exception;

use Cake\Core\Exception\Exception;

class MissingEnumStrategyException extends Exception
{
    protected $_templateMessage = 'Missing enum strategy class (%s)';
}
