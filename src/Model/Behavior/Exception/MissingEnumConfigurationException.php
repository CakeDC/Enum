<?php
namespace CakeDC\Enum\Model\Behavior\Exception;

use Cake\Core\Exception\Exception;

class MissingEnumConfigurationException extends Exception
{
    protected $_templateMessage = 'Missing enum configuration (%s)';
}
