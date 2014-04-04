<?php

namespace analyser;


require_once("ObjectTracker.php");
require_once("model\Type.php");
require_once("model\Scope.php");

class NameExtractor {
	private $classes = array();
	private $filename = "variableNames.serialized";
	private $functionName = "";
	private $tracker;
	

	public function __construct() {
		$this->tracker = new ObjectTracker();
		if (file_exists($this->filename)) {
			$oldData = file_get_contents($this->filename);
			$this->classes = unserialize($oldData);
		}


	}

	public function __destruct ( ) {
		//$this->stop();
	}

	public function start() {

		
		set_time_limit(2520);
		register_tick_function(array(&$this, 'tick'), true);
	}

	public function stop() {
		//analysis done, show data
		unregister_tick_function(array(&$profiler, 'tick'));
		
		file_put_contents($this->filename, serialize($this->classes));
		$this->writeCSV();
		$this->tracker->writeCSV();
		
	
	}

	public function tick($return = false)
	{
		
		$bt = debug_backtrace();
		$callIndex = 0;
		foreach ($bt as $call) {
			$callIndex++;

			//Do not go deeper since we have done that
			if ($callIndex > 2)
				continue;



	   		//find member variables of objects
		   	if (isset($call["object"])) {
		   		//the this object 
		   		$that = $call["object"];

		   		//do not analyse NameExtractor
		   		if ($that == $this)
		   			continue;
//var_dump($call);

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
					$this->recordArguments($call["args"], $rm, "$className.$functionName()");
		   		} else {
		   			try {
		   				if ($functionName != "" && $functionName != "require" && 
		   					$functionName != "require_once" &&
		   					$functionName != "include" && 
		   					$functionName != "wp_initial_constants") {
		   					
		   					if ($functionName != $this->functionName) {
		   						$this->functionName = $functionName;
				   				$rf = new \ReflectionFunction($functionName);
				   				$this->recordArguments($call["args"], $rf, "$functionName()");
			   				}
		   				}
		   			} catch (\ReflectionException $e) {

		   			}
		   		}
		   	}
		}

		
	   return TRUE;
	}

	private function recordArguments($arguments, $reflection, $className) {
		$pa = $reflection->getParameters();

		foreach ($arguments as $key => $variableValue) {
		
			if (isset($pa[$key])) {
				$this->recordParameter($className, $pa[$key]->getName(), $variableValue);
			}
		}
	}

	public function writeCSV() {
		$fileHandle = fopen($this->filename . ".csv", "w");

		foreach( $this->classes as $className => $class) {
			foreach( $class as $scope => $scopes) {
				foreach( $scopes as $variableName => $types) {
					foreach( $types as $type => $subtypes) {
						
						foreach( $subtypes as $subtype => $notUsed) {
							$type .= "<" . $subtype . ">";
						}
						fwrite($fileHandle, "$className;$scope;$type;$variableName\n");
					}
				}
			}
		}

		fclose($fileHandle);

		
	}


	private function recordParameter($className, $variableName, $value) {
		$this->recordVariable(Scope::getParameterScope(), $className, $variableName, $value);
	}

	private function recordProperty($className, $variableName, $value) {
		$this->recordVariable(Scope::getPropertyScope(), $className, $variableName, $value);
	}

	private function recordVariable(Scope $scope, $className, $variableName, $value) {
		if (isset($this->classes[$className]) == false) {
			$this->classes[$className] = array();
			
		}
		if (isset($this->classes[$className][$scope->toString()]) == false) {
			$this->classes[$className][$scope->toString()] = array();
		}

		if (isset($this->classes[$className][$scope->toString()][$variableName] ) == false) {
			$this->classes[$className][$scope->toString()][$variableName] = array();
		}

		if ($value != null) {
			//ignore array = true so only store an array in one variable...
			$type = $this->getType($value);

			if (is_object($value) == "object") {
				$this->tracker->trackObject($value, $variableName, $className, $scope, $type);
			}
			
			if (isset($this->classes[$className][$scope->toString()][$variableName][$type->toString()] ) == false) {
				$this->classes[$className][$scope->toString()][$variableName][$type->toString()] = array();
			}
			if ($type == "array"){
				$this->addArrayTypes($this->classes[$className][$scope->toString()][$variableName][$type->toString()], $value);
			}
		}
	}

	

	
	/**
	 * @param  String $value variable instance
	 * @return Type
	 */
	private function getType($variableInstance) {
		if (is_object($variableInstance) == "object") {
			return new Type(get_class($variableInstance));
		} else {
			return new Type(gettype($variableInstance));
		}
	}

	private function addArrayTypes(&$ret, $values, $ignoreArray = false) {
		if ($ignoreArray == false && 
			is_array ($values)) {

			$types = array();
			
			foreach ($values as $key => $instValue) {
				$types[$this->getType($instValue)] = true;
			}
			foreach ($types as $key => $instValue) {
				$ret[$key] = $key;
			}
			
		}
	}


}


