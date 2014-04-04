<?php

namespace analyser;



/**
 * The class and function
 */
class ExecutionContext {

	/**
	 * @var String
	 */
	private $class;
	private $function;
	private $scope;
	private $parameterTypeHint = "";
	private $documentation = "";

	public function __construct( $class, $function, Scope $scope) {
		if (is_string($class) == false || is_string($function) == false) {
			throw new \Exception("Should be string");
		}
		$this->class = $class;
		$this->function = $function;
		$this->scope = $scope;
		
	}

	public function setTypeHint($hint) {
		if ($hint != null)
			$this->parameterTypeHint = $hint->getName();
	}
	public function setDocumentation($docs) {
		if ($docs != null)
			$this->documentation = $docs;	
	}

	public function toString() {
		return $this->class . "." . $this->function . " " . $this->scope->toString() . " " . $this->parameterTypeHint . " " . $this->documentation;
	}
}