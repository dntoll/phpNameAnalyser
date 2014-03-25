<?php

namespace profiler;


class Profiler {
	private $classes = array();
	private $parameters = array();

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

		   	if (isset($call["args"])) {
		   		if (isset($call["class"])) {
		   			//var_dump($call["args"]);
		   			$className = $call["class"];
		   			$functionName = $call["function"];
		   			foreach ($call["args"] as $key => $variableValue) {

		   				$rm = $ro->getMethod($functionName);

						$rm->setAccessible(true);
		   				$pa = $rm->getParameters();

		   				if (isset($pa[$key]))
		   					$this->recordParameter($className, $functionName, $pa[$key]->getName(), $variableValue);
		   				//else
		   					//assert(false);
		   			}
		   		}
		   	}
		}
	   return TRUE;
	}

	public function show() {
		//var_dump($this->classes);
		foreach( $this->classes as $name => $class) {
			//echo "$name ";
			foreach( $class as $scope => $scopes) {
				//echo " [ ";
				foreach( $scopes as $name => $variable) {
					echo "$scope $name (";
					foreach( $variable as $name => $value) {
						echo "$value ";
					}
					echo ") <br/>";
					
				}
				//echo " ]";
			}
			//echo " <br/>";
		}
	}


	private function recordParameter($className, $functionName, $variableName, $value) {
		$this->recordVariable("parameters", $className, $variableName, $value);
	}

	private function recordProperty($className, $variableName, $value) {
		$this->recordVariable("properties", $className, $variableName, $value);
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


