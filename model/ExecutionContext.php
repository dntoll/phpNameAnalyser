<?php

namespace analyser;



/**
 * The class and function
 */
abstract class AbstractExecutionContext {
	public abstract function getFunction();
	public abstract function getScope();

	private $names = array();

	public function getByName(VariableName $name, Comment $comment) {
		if (isset($this->names[$name->toString()]) == false) {
			$this->names[$name->toString()] = new VariableDeclaration($name, $comment);	
		}
		
		return $this->names[$name->toString()];
	}

	public function getNames() {
		return $this->names;
	}


}

class FunctionParameterContext extends AbstractExecutionContext {
	protected $function;

	public function __construct($function) {
		if (is_string($function) == false) {
			throw new \Exception("Should be string");
		}
		$this->function = $function;
	}

	public function getFunction() {
		return $this->function;
	}
	public function getScope() {
		return "parameter";
	}

	
}

class MethodParameterContext extends FunctionParameterContext{
	protected $class;

	public function __construct($class, $function) {
		parent::__construct($function);
		if (is_string($class) == false) {
			throw new \Exception("Should be string");
		}
		$this->class = $class;

	}

	public function getFunction() {
		return $this->class . "." . $this->function;
	}


}

class ClassContext extends AbstractExecutionContext{
	protected $class;

	public function __construct($class) {
		if (is_string($class) == false) {
			throw new \Exception("Should be string");
		}
		$this->class = $class;
	}

	public function getFunction() {
		return $this->class;
	}
	public function getScope() {
		return "property";
	}

	
}