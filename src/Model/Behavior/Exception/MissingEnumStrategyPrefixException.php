<?php
namespace Enum\Model\Behavior\Exception;

use Cake\Core\Exception\Exception;

class MissingEnumStrategyPrefixException extends Exception
{
    protected $_templateMessage = 'Missing prefix for strategy (%s)';
}