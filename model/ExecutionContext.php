<?php

namespace analyser;



/**
 * The class and function
 */
abstract class AbstractExecutionContext {

	
	private $documentation = "";

	protected function setDocumentation($docs) {
		if ($docs != null)
			$this->documentation = $docs;	
	}

	protected function getCommentOn(VariableName $variableName) {

		$lines = explode("\n", $this->documentation);

		$ret = "";
		foreach ($lines as $line) {
			if(strpos($line, "$". $variableName->toString()) !== FALSE) {
				$ret .= "C[" . trim($line) . "]";
			}
		}

		return $ret;
	}

	public abstract function toString(VariableName $variableName);

	public abstract function getFunction();
	public abstract function getScope();
}

class FunctionParameterContext extends AbstractExecutionContext {
	protected $function;
	protected $parameterTypeHint = "";

	public function setTypeHint($hint) {
		if ($hint != null)
			$this->parameterTypeHint = "T[" . $hint->getName() . "]";
	}

	public function __construct($function, $docs) {
		if (is_string($function) == false) {
			throw new \Exception("Should be string");
		}
		$this->function = $function;
		$this->setDocumentation($docs);
	}

	public function toString(VariableName $variableName) {
		return $this->function . " " . $this->parameterTypeHint . " " . $this->getCommentOn($variableName);
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

	public function __construct($class, $function, $docs) {
		parent::__construct($function, $docs);
		if (is_string($class) == false) {
			throw new \Exception("Should be string");
		}
		$this->class = $class;

	}

	public function toString(VariableName $variableName) {
		return $this->getFunction() . " " . $this->parameterTypeHint . " " . $this->getCommentOn($variableName);
	}

	public function getFunction() {
		return $this->class . "." . $this->function;
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


	public function toString(VariableName $variableName) {
		return $this->class  . " " . $this->getCommentOn($variableName);
	}

	public function getFunction() {
		return $this->class;
	}
	public function getScope() {
		return "property";
	}
}