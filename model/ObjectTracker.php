<?php

namespace analyser;

class ObjectTracker {

	private $objects = array();

	public function __construct($fileName) {
		assert(is_string($fileName));

		$this->saveFile = $fileName;
	}

	public function trackObject(Instance $instance, VariableDeclaration $decl) {
		$type = $instance->getType();
		$ref = $instance->getObjectRef();
		$objectRef = $type->toString() . "-" . $ref;

		if (isset($this->objects[$objectRef]) == false) {
			$this->objects[$objectRef] = array();
		}

		if (isset($this->objects[$objectRef][$decl->getContextFunction()]) == false) {
			$this->objects[$objectRef][$decl->getContextFunction()] = $decl;

			loggThis("tracked object " . $type->toString(). " " . $decl->getName()->toString() . " [$ref] in " . $decl->getContextFunction());
		}

		
	}

	public function writeCSV() {
		$fileHandle = fopen($this->saveFile, "w");
		foreach ($this->objects as $objectRef => $contexts) {
			foreach ($contexts as $functionContext => $variableDeclaration) {
				$name = $variableDeclaration->getName()->toString();
				fwrite($fileHandle, "$objectRef; $name; $functionContext; \n");
				//var_dump("$objectRef; $name; $functionContext \n");
				
			}
			
		}
		//var_dump($this->objects);

		fclose($fileHandle);
	}

	
}