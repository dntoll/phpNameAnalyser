<?php

namespace analyser;



/**
 * The class and function
 */
abstract class AbstractExecutionContext {

	
	protected $documentation = "";

	protected function setDocumentation($docs) {
		if ($docs != null)
			$this->documentation = $docs;	
	}

	public abstract function toString();
}

class FunctionParameterContext extends AbstractExecutionContext {
	protected $function;
	protected $parameterTypeHint = "";

	public function setTypeHint($hint) {
		if ($hint != null)
			$this->parameterTypeHint = $hint->getName();
	}

	public function __construct($function, $docs) {
		if (is_string($function) == false) {
			throw new \Exception("Should be string");
		}
		$this->function = $function;
		$this->setDocumentation($docs);
	}

	public function toString() {
		return $this->function . " " . $this->parameterTypeHint . " " . $this->documentation;
	}
}

class MethodParameterContext extends FunctionParameterContext{
	protected $class;

	public function __construct($class, $function, $docs) {
		parent::__construct($function, $docs);
		if (is_string($class) == false) {
			throw new \Exception("Should be string");
		}
		$this->class = $class;

	}

	public function toString() {
		return $this->class . "." . $this->function . " " . $this->parameterTypeHint . " " . $this->documentation;
	}
}

class PropertyContext extends AbstractExecutionContext{
	protected $class;

	public function __construct($class, $docs) {
		if (is_string($class) == false) {
			throw new \Exception("Should be string");
		}
		$this->class = $class;
		$this->setDocumentation($docs);
	}


	public function toString() {
		return $this->class  . " " . $this->documentation;
	}
}