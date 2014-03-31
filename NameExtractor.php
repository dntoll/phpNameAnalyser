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

		
		set_time_limit(2520);
		register_tick_function(array(&$this, 'tick'), true);
		declare(ticks=1);
		$this->f = fopen("functionspy.txt", "a");
		$this->functionName = "";
	}

	public function stop() {
		//analysis done, show data
		unregister_tick_function(array(&$profiler, 'tick'));
		
		file_put_contents($this->filename, serialize($this->classes));
		$this->writeCSV();
		fclose($this->f);
		
	}

	public function tick($return = false)
	{
		
		$bt = debug_backtrace();
		
	   
	   foreach ($bt as $call) {
	   		//find member variables of objects
		   	if (isset($call["object"])) {
		   		//the this object 
		   		$that = $call["object"];

		   		if ($that == $this)
		   			continue;
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
		   					//echo "ReflectionFunction $functionName \n";
		   					//
		   					
		   					if ($functionName != $this->functionName) {
		   						$this->functionName = $functionName;
			   					//fwrite($this->f, $functionName . "\n");
			   					
				   				$rf = new \ReflectionFunction($functionName);
				   				$this->recordArguments($call["args"], $rf, "$functionName()");
			   				}
			   				//
		   				}
		   			} catch (\ReflectionException $e) {

		   			}
		   		}
		   	}
		}

		
	   return TRUE;
	}

	private function recordArguments($arguments, $reflection, $className) {
		foreach ($arguments as $key => $variableValue) {
			$pa = $reflection->getParameters();


			if (isset($pa[$key])) {
				//var_dump($pa[$key]->getClass());
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
		$this->recordVariable("parameter", $className, $variableName, $value);
	}

	private function recordProperty($className, $variableName, $value) {
		$this->recordVariable("property", $className, $variableName, $value);
	}

	private function recordVariable($scope, $className, $variableName, $value) {
		if (isset($this->classes[$className]) == false) {
			$this->classes[$className] = array();
			
		}
		if (isset($this->classes[$className][$scope]) == false) {
			$this->classes[$className][$scope] = array();
		}

		if (isset($this->classes[$className][$scope][$variableName] ) == false) {
			$this->classes[$className][$scope][$variableName] = array();
		}

		if ($value != null) {
			//ignore array = true so only store an array in one variable...
			$type = $this->getType($value);


			if (isset($this->classes[$className][$scope][$variableName][$type] ) == false) {
				$this->classes[$className][$scope][$variableName][$type] = array();
			}
			if ($type == "array"){
				$this->addArrayTypes($this->classes[$className][$scope][$variableName][$type], $value);
			}
		}
	}

	private function getType($value) {
		if (is_object($value) == "object")
			return get_class($value);
		else {
			return gettype($value);
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


