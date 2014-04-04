<?php

namespace analyser;

class ObjectTracker {

	private $objects = array();

	public function trackObject(Instance $value, VariableName $variableName, AbstractExecutionContext $className, Type $type) {
		$objectRef = $type->toString() . "-" . $value->getObjectRef();
		if (isset($this->objects[$objectRef]) == false) {
			$this->objects[$objectRef] = array();
		}

		if (isset($this->objects[$objectRef][$variableName->toString()]) == false) {
			$this->objects[$objectRef][$variableName->toString()] = array();
		}
		$this->objects[$objectRef][$variableName->toString()][md5($className->toString($variableName))] =$className->toString($variableName);
	}

	public function writeCSV() {
		$fileHandle = fopen("objects.csv", "w");
		foreach ($this->objects as $objectRef => $objectInstance) {
			foreach ($objectInstance as $name => $usages) {
				foreach ($usages as $usage => $notUsed) {
					fwrite($fileHandle, "$objectRef; $name; $notUsed \n");
					//var_dump("$objectRef; $name; $notUsed \n");
				}
			}
			
		}
		var_dump($this->objects);

		fclose($fileHandle);
	}

	
}