<?php

namespace analyser;

class Instance {

	public function __construct(&$variableValue) {
		$this->variableValue = &$variableValue;
	}

	/**
	 * @param  String $value variable instance
	 * @return Type
	 */
	public  function getType() {
		if (is_object($this->variableValue) == "object") {
			return new Type(get_class($this->variableValue));
		} else {
			return new Type(gettype($this->variableValue));
		}
	}

	public function isObject() {
		return is_object($this->variableValue);
	}

	public function getObjectRef() {
		
		ob_start();
		var_dump($this->variableValue);
		$content = ob_get_clean();
		//Two different outputs with and without HTML tags
		//object(Exception)[20]
		//object(Exception)#20 (7)

		$startOfString = "object(" . get_class($this->variableValue) . ")";
		
		$content = trim(strip_tags($content)); //remove xdebug tags

		$contentWithoutStartString = substr($content, strlen($startOfString) + 1); 
		return intval($contentWithoutStartString);

	}

	public function getValue() {
		return $this->variableValue;
	}

	public function getArrayTypes() {
		$ret = array();
		if (is_array ($this->variableValue)) {

			$types = array();
			
			foreach ($this->variableValue as $key => $instValue) {
				$childInstance = new Instance($instValue);
				$types[$childInstance->getType()->toString()] = true;
			}
			foreach ($types as $key => $instValue) {
				$ret[$key] = $key;
			}
			
		} else if (is_object($this->variableValue)) {

			$refC = new \ReflectionClass(get_class($this->variableValue));

			$interfaces = $refC->getInterfaceNames();
			$parents = array();

			$class = $refC;
			while ($parent = $class->getParentClass()) {
			    $parents[] = $parent->getName();
			    $class = $parent;
			}

			$types = array();
			
			foreach ($interfaces as $interface) {
				$types[$interface] = true;
			}
			foreach ($parents as $interface) {
				$types[$interface] = true;
			}
			foreach ($types as $key => $instValue) {
				$ret[$key] = $key;
			}
		}
		return $ret;
	}
}