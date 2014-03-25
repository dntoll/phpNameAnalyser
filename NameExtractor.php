<?php

namespace analyser;


class NameExtractor {
	private $classes = array();
	private $filename = "variableNames.serialized";

	public function __construct() {
		
		if (file_exists($this->filename)) {
			$oldData = file_get_contents($this->filename);
			$this->classes = unserialize($oldData);
		}
	}

	public function start() {

		
		set_time_limit(120);
		register_tick_function(array(&$this, 'tick'), true);
		declare(ticks=1);
	}

	public function stop() {
		//analysis done, show data
		unregister_tick_function(array(&$profiler, 'tick'));
		
		file_put_contents($this->filename, serialize($this->classes));
		writeCSV();
		
	}

	public function tick($return = false)
	{
		$bt = debug_backtrace();
		
	   
	   foreach ($bt as $call) {
	   		//find member variables of objects
		   	if (isset($call["object"])) {
		   		//the this object 
		   		$that = $call["object"];
		   		$ro = new \ReflectionObject($that);
		   		$props   = $ro->getProperties();

		   		foreach ($props  as $property) {
		   			$property->setAccessible(true);
		   			$this->recordProperty(get_class($that), $property->name, $property->getValue($that));
		   			$property->setAccessible(false);
		   		}

		   	}

			$functionName = $call["function"];
		   	if (isset($call["args"])) {
		   		if (isset($call["class"])) {
		   			$className = $call["class"];
		   			$ro = new \ReflectionClass($className);
		   			$rm = $ro->getMethod($functionName);
					$rm->setAccessible(true);
					$this->recordArguments($call["args"], $rm, $className, $functionName);
		   		} else {
		   			try {
		   				$rf = new \ReflectionFunction($functionName);
		   				$this->recordArguments($call["args"], $rf, "function", $functionName);
		   			} catch (\ReflectionException $e) {

		   			}
		   		}
		   	}
		}
	   return TRUE;
	}

	private function recordArguments($arguments, $reflection, $className, $functionName) {
		foreach ($arguments as $key => $variableValue) {
			$pa = $reflection->getParameters();
			if (isset($pa[$key]))
				$this->recordParameter($className, $functionName, $pa[$key]->getName(), $variableValue);
		}
	}

	public function writeCSV() {
		$fileHandle = fopen($this->filename . ".csv", "w");

		foreach( $this->classes as $className => $class) {
			foreach( $class as $scope => $scopes) {
				foreach( $scopes as $name => $variable) {
					foreach( $variable as $value) {
						fwrite($fileHandle, "$className;$scope;$value;$name\n");
					}
				}
			}
		}

		fclose($fileHandle);
	}


	private function recordParameter($className, $functionName, $variableName, $value) {
		$this->recordVariable("parameter", $className, $variableName, $value);
	}

	private function recordProperty($className, $variableName, $value) {
		$this->recordVariable("property", $className, $variableName, $value);
	}

	private function recordVariable($type, $className, $variableName, $value) {
		if (isset($this->classes[$className]) == false) {
			$this->classes[$className] = array();
			
		}
		if (isset($this->classes[$className][$type]) == false) {
			$this->classes[$className][$type] = array();
		}

		if (isset($this->classes[$className][$type][$variableName] ) == false) {
			$this->classes[$className][$type][$variableName] = array();
		}

		
		$this->classes[$className][$type][$variableName][$this->getType($value)] = $this->getType($value);
	}

	private function getType($value) {
		if (is_object($value) == "object")
			return get_class($value);
		else
			return gettype($value);
	}
}


