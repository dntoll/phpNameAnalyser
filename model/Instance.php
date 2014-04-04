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

		$startpos = strpos($content, "[")+4; //remove [<i>
		$objectRef = substr($content, $startpos, strpos($content, "]")-$startpos-4); //remove ]</i>
		
		return $objectRef;
	}
}