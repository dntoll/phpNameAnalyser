<?php

namespace analyser;

class ObjectTracker {
	private function trackObject($value, $variableName, $className, $scope) {
		$objectRef = $type . "-" .$this->getObjectRef($value);
		if (isset($this->objects[$objectRef]) == false) {
			$this->objects[$objectRef] = array();
		}

		if (isset($this->objects[$objectRef][$variableName]) == false) {
			$this->objects[$objectRef][$variableName] = array();
		}
		$this->objects[$objectRef][$variableName][$className . " $scope"] ="";
	}
}