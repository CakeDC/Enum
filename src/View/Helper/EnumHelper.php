<?php
namespace CakeDC\Enum\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

/**
 * Enumeration List Helper.
 */
class EnumHelper extends Helper {

	private $className;

	/**
	 * Helper dependencies
	 *
	 * @var array
	 */
	public $helpers = ['Form'];

	/**
	 * Get Enumeration list
	 *
	 * @param string $fieldName This should be "modelname.fieldname"
	 * @param string $tableName if fieldName contain "modelname", it not need to be passed.
     * @param array $options Each type of input takes different options, here should be passed the prefix and the strategy
     * @throws RuntimeException
	 * @return array
	 */
	public function input($fieldName, $tableName = '', $options = []) {
		if (strpos($fieldName, '.') !== false && empty($tableName)) {
			$name = explode('.', $fieldName);
			$tableName = reset($name);
		}

		if (!isset($options['alias']) && empty($options['alias'])) {
			throw new \RuntimeException('You need to set the alias defined in the table!');
		}
		$strategy = $options['alias'];
		unset($options['alias']);
		$Table = $this->tableInstance($tableName);
		$result = $Table->enum($strategy);
		$options['options'] = $result;
		return $this->Form->input($fieldName, $options);
	}

	/**
	 * Get instance of the a strategy
	 *
	 * @param string $tableName if fieldName contain "modelname", it not need to be passed.
	 * @throws RuntimeException
	 * @return Object
	 */
	private function tableInstance($tableName) {
		$tableName = ucfirst($tableName);

		if (empty($this->className)) {
			$class = TableRegistry::get($tableName);
		} else {
			$class = TableRegistry::get($tableName, [
				'className' => $this->getClassName()
			]);
		}

		return $class;
	}

	/**
	 * Setting class name to put the real place of the class
	 *
	 * @param string $path path of the class
	 * @return void
	 */
	public function setClassName($className) {
		$this->className = $className;
	}

	/**
	 * Getting class name
	 *
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}
}