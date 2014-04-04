<?php

namespace analyser;

class ObjectTracker {

	private $objects = array();

	public function trackObject($value, $variableName, $className, Scope $scope, Type $type) {
		$objectRef = $type->toString() . "-" .$this->getObjectRef($value);
		if (isset($this->objects[$objectRef]) == false) {
			$this->objects[$objectRef] = array();
		}

		if (isset($this->objects[$objectRef][$variableName]) == false) {
			$this->objects[$objectRef][$variableName] = array();
		}
		$this->objects[$objectRef][$variableName][$className . " " . $scope->toString()] ="";
	}

	public function writeCSV() {
		$fileHandle = fopen("objects.csv", "w");
		foreach ($this->objects as $objectRef => $objectInstance) {
			foreach ($objectInstance as $name => $usages) {
				foreach ($usages as $usage => $notUsed) {
					fwrite($fileHandle, "$objectRef; $name; $usage \n");
				}
			}
			
		}
		var_dump($this->objects);

		fclose($fileHandle);
	}

	private function getObjectRef($object) {

		ob_start();
		var_dump($object);
		$content = ob_get_clean();

		$startpos = strpos($content, "[")+4; //remove [<i>
		$objectRef = substr($content, $startpos, strpos($content, "]")-$startpos-4); //remove ]</i>
		
		return $objectRef;
	}
}