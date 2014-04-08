<?php

namespace analyser;

class Instance {

	public function __construct(&$variableValue) {
		$this->variableValue = &$variableValue;
		/*if ($variableValue == null) {
			throw new \Exception("not an instance");
		}*/
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


		/*echo "[";
		var_dump($this->variableValue);
		echo "]";*/

		//object(Exception)[20]
		//object(Exception)#20 (7)
		//
		$startOfString = "object(" . get_class($this->variableValue) . ")";
		
		$content = trim(strip_tags($content)); //remove xdebug tags

		//echo "[$startOfString] and [$content]";
		$contentWithoutStartString = substr($content, strlen($startOfString) + 1); 
		return intval($contentWithoutStartString);

		/*$startpos = strpos($content, "[")+4; //remove [<i>
		$objectRef = substr($content, $startpos, strpos($content, "]")-$startpos-4); //remove ]</i>

		if (is_numeric($objectRef) == FALSE) {
			//object(Exception)#20
			throw new \Exception($this->variableValue);
		}
		
		return $objectRef;*/
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
			
		}
		return $ret;
	}
}