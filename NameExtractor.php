<?php

namespace analyser;


require_once("ObjectTracker.php");
require_once("model\Type.php");
require_once("model\Scope.php");
require_once("model\Instance.php");
require_once("model\VariableName.php");
require_once("model\ExecutionContext.php");


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

		   		$ro = new \ReflectionObject($that);
		   		$props   = $ro->getProperties();

		   		foreach ($props  as $property) {
		   			$property->setAccessible(true);
		   			$value = $property->getValue($that);
		   			$ec = new ExecutionContext(get_class($that), "", Scope::getPropertyScope());
		   			$ec->setDocumentation($property->getDocComment());
		   			$this->recordVariable($ec, 
		   									new VariableName($property->name), 
		   									new Instance($value));

		   			$property->setAccessible(false);


		   		}

		   	}

			

		   	if (isset($call["args"])) {
		   		$functionName = $call["function"];
		   		if (isset($call["class"])) {
		   			$className = $call["class"];
		   			$ro = new \ReflectionClass($className);
		   			$rm = $ro->getMethod($functionName);


					$rm->setAccessible(true);
					$ec = new ExecutionContext($className, $functionName, Scope::getParameterScope());
					$ec->setDocumentation($rm->getDocComment());

					$this->recordArguments($call["args"], $rm, $ec);
		   		} else {
		   			try {
		   				if ($functionName != "" && 
		   					$functionName != "require" && 
		   					$functionName != "require_once" &&
		   					$functionName != "include" && 
		   					$functionName != "wp_initial_constants") {
		   					
		   					if ($functionName != $this->functionName) {
		   						$this->functionName = $functionName;
				   				$rf = new \ReflectionFunction($functionName);
				   				$this->recordArguments($call["args"], $rf, new ExecutionContext("", $functionName, Scope::getParameterScope()));
			   				}
		   				}
		   			} catch (\ReflectionException $e) {

		   			}
		   		}
		   	}
		}

		
	   return TRUE;
	}

	private function recordArguments($arguments, \Reflector $reflection, ExecutionContext $className) {
		$pa = $reflection->getParameters();

		foreach ($arguments as $key => $variableValue) {
		
			if (isset($pa[$key])) {
				//var_dump($pa[$key]->getClass());
				$className->setTypeHint($pa[$key]->getClass());
				$this->recordVariable($className, new VariableName($pa[$key]->getName()), new Instance($variableValue));
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


	private function recordVariable(ExecutionContext $className, VariableName $variableName, Instance $value) {
		if (isset($this->classes[$className->toString()]) == false) {
			$this->classes[$className->toString()] = array();
			
		}
		if (isset($this->classes[$className->toString()]) == false) {
			$this->classes[$className->toString()][$scope->toString()] = array();
		}

		if (isset($this->classes[$className->toString()][$variableName->toString()] ) == false) {
			$this->classes[$className->toString()][$variableName->toString()] = array();
		}

		if ($value != null) {
			//ignore array = true so only store an array in one variable...
			$type = $value->getType();

			if ($value->isObject()) {
				$this->tracker->trackObject($value, $variableName, $className, $type);
			}
			
			if (isset($this->classes[$className->toString()][$variableName->toString()][$type->toString()] ) == false) {
				$this->classes[$className->toString()][$variableName->toString()][$type->toString()] = array();
			}
			if ($type == "array"){
				$this->addArrayTypes($this->classes[$className->toString()][$variableName->toString()][$type->toString()], $value);
			}
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


