<?php

namespace analyser;

class ObjectTracker {

	private $objects = array();

	public function __construct($fileName) {
		assert(is_string($fileName));

		$this->saveFile = $fileName;
	}

	public function trackObject(Instance $instance, VariableName $variableName, AbstractExecutionContext $context, Type $type, VariableDeclaration $decl) {
		$ref = $instance->getObjectRef();
		$objectRef = $type->toString() . "-" . $ref;
		if (isset($this->objects[$objectRef]) == false) {
			$this->objects[$objectRef] = array();
		}

		if (isset($this->objects[$objectRef][$variableName->toString()]) == false) {
			$this->objects[$objectRef][$variableName->toString()] = array();
		}
		$this->objects[$objectRef][$variableName->toString()][$context->getFunction()] = $decl;

		loggThis("tracked object ".$type->toString(). " " . $variableName->toString() . " [$ref] in " . $context->getFunction(), $decl);
	}

	public function writeCSV() {
		$fileHandle = fopen($this->saveFile, "w");
		foreach ($this->objects as $objectRef => $objectInstance) {
			foreach ($objectInstance as $name => $usages) {
				foreach ($usages as $function => $decl) {
					fwrite($fileHandle, "$objectRef; $name; $function; \n");
					//var_dump("$objectRef; $name; $notUsed \n");
				}
			}
			
		}
		//var_dump($this->objects);

		fclose($fileHandle);
	}

	
}