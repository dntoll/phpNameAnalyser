<?php

namespace analyser;


require_once("model\ObjectTracker.php");
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

		   		$this->addProperties($that);

		   	}

			

		   	if (isset($call["args"])) {
		   		
		   		if (isset($call["class"])) {
		   			$this->addMethodParameters($call);
		   		} else {
		   			$this->addFunctionParameters($call);
		   		}
		   	}
		}

		
	   return TRUE;
	}

	private function addFunctionParameters(&$call) {
		try {
			$functionName = $call["function"];
			if ($functionName != "" && 
				$functionName != "require" && 
				$functionName != "require_once" &&
				$functionName != "include" && 
				$functionName != "wp_initial_constants" &&
				$functionName != "wp_plugin_directory_constants" ) {
   					
				if ($functionName != $this->functionName) {
					$this->functionName = $functionName;
	  				$rf = new \ReflectionFunction($functionName);
	  				$ec = new FunctionParameterContext($functionName, $rf->getDocComment());
		   			$this->recordArguments($call["args"], $rf, $ec);
	   			}
   			}
   		} catch (\ReflectionException $e) {
		}
	}

	private function addMethodParameters(&$call) {
		$functionName = $call["function"];
		$className = $call["class"];
		$ro = new \ReflectionClass($className);
		$rm = $ro->getMethod($functionName);
		$rm->setAccessible(true);
		$ec = new MethodParameterContext($className, $functionName, $rm->getDocComment());

		$this->recordArguments($call["args"], $rm, $ec);
	}

	private function addProperties(&$that) {
		$ro = new \ReflectionObject($that);
   		$props   = $ro->getProperties();

   		foreach ($props  as $property) {
   			$property->setAccessible(true);
   			$value = $property->getValue($that);

   			if ($value != null) {
	   			$ec = new PropertyContext(get_class($that), $property->getDocComment());
	   			
	   			$this->recordVariable($ec, 
	   									new VariableName($property->name), 
	   									new Instance($value));
	   			
	   			$property->setAccessible(false);
   			}
   		}
	}



	private function recordArguments($arguments, \Reflector $reflection, AbstractExecutionContext $className) {
		$pa = $reflection->getParameters();

		foreach ($arguments as $key => $variableValue) {
		
			if (isset($pa[$key]) && $variableValue != null) {
				//var_dump($pa[$key]->getClass());
				$className->setTypeHint($pa[$key]->getClass());
				
				$this->recordVariable($className, new VariableName($pa[$key]->getName()), new Instance($variableValue));
				
			}
		}
	}

	public function writeCSV() {
		$fileHandle = fopen($this->filename . ".csv", "w");

		foreach( $this->classes as $className => $variables) {
			foreach( $variables as $variableName => $types) {
				foreach( $types as $type => $subtypes) {
					
					foreach( $subtypes as $subtype => $notUsed) {
						$type .= "<" . $subtype . ">";
					}
					fwrite($fileHandle, "$className;$variableName;$type\n");
				}
			}
			
		}

		fclose($fileHandle);

		
	}


	private function recordVariable(AbstractExecutionContext $className, VariableName $variableName, Instance $value) {

		$contextString = $className->getFunction();

		if (isset($this->classes[$contextString]) == false) {
			$this->classes[$contextString] = array();
			
		}
		
		if (isset($this->classes[$contextString][$variableName->toString()] ) == false) {
			$this->classes[$contextString][$variableName->toString()] = array();
		}

		
		//ignore array = true so only store an array in one variable...
		$type = $value->getType();

		if ($value->isObject()) {
			$this->tracker->trackObject($value, $variableName, $className, $type);
		}
		
		if (isset($this->classes[$contextString][$variableName->toString()][$type->toString()] ) == false) {
			$this->classes[$contextString][$variableName->toString()][$type->toString()] = array();
		}

		
		$arrayTypes = $value->getArrayTypes();
		foreach ($arrayTypes as $key => $typeInArray) {
			$this->classes[$contextString][$variableName->toString()][$type->toString()][$typeInArray] = $typeInArray;
		}
			
		
	}
}


