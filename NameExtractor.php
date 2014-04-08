<?php

namespace analyser;


require_once("model/ObjectTracker.php");
require_once("model/Type.php");
require_once("model/Scope.php");
require_once("model/Instance.php");
require_once("model/VariableName.php");
require_once("model/ExecutionContext.php");
require_once("model/VariableDeclaration.php");
require_once("model/Comment.php");
require_once("../phpLoggerLib/Logger.php");


class NameExtractor {
	private $classes = array();
	private $filename = "data/variableNames";
	private $functionName = "";
	private $tracker;
	

	public function __construct($doLoad = true, $file = "data/variableNames") {
		$this->filename = $file;
		$this->tracker = new ObjectTracker($file . "_objects");
		if ($doLoad && file_exists($this->filename) ) {
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
		unregister_tick_function(array(&$this, 'tick'));
		
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
				//loggThis("tick", $call);
		   	}

			

		   	if (isset($call["args"])) {
		   		//loggThis("tick", $call);
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
   					
				//if ($functionName != $this->functionName) {
					$this->functionName = $functionName;
	  				$rf = new \ReflectionFunction($functionName);
	  				$ec = new FunctionParameterContext($functionName);
		   			$this->recordArguments($call["args"], $rf, $ec, new Comment($rf->getDocComment()));
	   			//}
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
		$ec = new MethodParameterContext($className, $functionName);

		$this->recordArguments($call["args"], $rm, $ec, new Comment($rm->getDocComment()));
	}

	private function addProperties(&$that) {
		$ro = new \ReflectionObject($that);
   		$props   = $ro->getProperties();

   		foreach ($props  as $property) {
   			$property->setAccessible(true);
   			$value = $property->getValue($that);

   			if ($value != null) {
	   			$ec = new ClassContext(get_class($that));
	   			
	   			$this->recordVariable($ec, 
	   									new VariableName($property->name), 
	   									new Instance($value),
	   									"",
	   									new Comment($property->getDocComment()));
	   			
	   			$property->setAccessible(false);
   			}
   		}
	}



	private function recordArguments($arguments, 
									\Reflector $reflection, 
									AbstractExecutionContext $className, 
									Comment $comment) {
		$pa = $reflection->getParameters();

		foreach ($arguments as $key => $variableValue) {
		
			if (isset($pa[$key]) && $variableValue != null) {
				
				$name = new VariableName($pa[$key]->getName());
				$this->recordVariable($className, 
									  	$name, 
									  	new Instance($variableValue), 
									  	$pa[$key]->getClass(),
									  	$comment->getCommentOn($name));
				
			}
		}
	}

	public function writeCSV() {
		$fileHandle = fopen($this->filename . ".csv", "w");

		//ini_set('xdebug.var_display_max_depth', 6 );
		//xdebug_var_dump($this->classes );
		foreach( $this->classes as $className => $context) {
			$names = $context->getNames();
			foreach($names  as $variableName => $name) {
				$types = $name->getTypes();
				$comment = $name->getCommentOn($name->getName());
				

				foreach( $types as $typeName => $arrayTypes) {

					
					foreach( $arrayTypes as $subtype => $notUsed) {
						$typeName .= "<" . $subtype . ">";
					}

					fwrite($fileHandle, "$className;$variableName;$typeName;" . $comment->toString() .";T[" .  $name->getTypeHint() . "]\n");

					loggThis("found instance in $className", array(	"context" => $className, 
																	"name" => $variableName, 
																	"dynamic type" => $typeName, 
																	"dynamic_subtype" => $arrayTypes,
																	"comment"=>$comment, 
																	"typehint" => $name->getTypeHint()), false);
				}
			}
			
		}

		fclose($fileHandle);

		
	}


	private function recordVariable(AbstractExecutionContext $context, VariableName $variableName, Instance $value, $typeHint, Comment $comment) {
		$contextString = $context->getFunction();
		if (isset($this->classes[$contextString]) == false)  {
			$this->classes[$contextString] = $context;
		} else {
			$context = $this->classes[$contextString];
		}


		$type = $value->getType();
		$arrayTypes = $value->getArrayTypes();

		$variableDeclaration = $context->getByName($variableName, $comment);

		$variableDeclaration->addType($type, $arrayTypes);

		$variableDeclaration->setTypeHint($typeHint);

		if ($value->isObject()) {
			$this->tracker->trackObject($value, $variableDeclaration);

			$this->addProperties($value->variableValue);
		}
	}
}


