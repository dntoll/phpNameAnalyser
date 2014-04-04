<?php

namespace analyser;


class NameExtractor {
	private $classes = array();
	private $filename = "variableNames.serialized";
	private $functionName = "";

	private $objects = array();

	public function __construct() {
		
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
		foreach ($arguments as $key => $variableValue) {
			$pa = $reflection->getParameters();


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

			if (is_object($value) == "object") {
				$objectRef = $type . "-" .$this->getObjectRef($value);
				if (isset($this->objects[$objectRef]) == false) {
					$this->objects[$objectRef] = array();
				}

				if (isset($this->objects[$objectRef][$variableName]) == false) {
					$this->objects[$objectRef][$variableName] = array();
				}
				$this->objects[$objectRef][$variableName][$className . " $scope"] ="";
			}
			


			if (isset($this->classes[$className][$scope][$variableName][$type] ) == false) {
				$this->classes[$className][$scope][$variableName][$type] = array();
			}
			if ($type == "array"){
				$this->addArrayTypes($this->classes[$className][$scope][$variableName][$type], $value);
			}
		}
	}

	private function getObjectRef($object) {

		ob_start();
		var_dump($object);
		$content = ob_get_clean();

		$startpos = strpos($content, "[")+4; //remove [<i>
		$objectRef = substr($content, $startpos, strpos($content, "]")-$startpos-4); //remove ]</i>
		
		return $objectRef;
	}

	private function getType($value) {
		if (is_object($value) == "object") {
		

			return get_class($value);
		} else {
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


