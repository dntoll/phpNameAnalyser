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

	

	public abstract function toString(VariableName $variableName);

	public abstract function getFunction();
	public abstract function getScope();

	private $names = array();
	public function getByName(VariableName $name) {
		if (isset($this->names[$name->toString()]) == false) {
			$this->names[$name->toString()] = new NamedTypeList($name);	
		}
		
		return $this->names[$name->toString()];
	}

	public function getNames() {
		return $this->names;
	}


}

class FunctionParameterContext extends AbstractExecutionContext {
	protected $function;
	protected $parameterTypeHint = "";


	Type hints and variable names are on named Types?
	The context collects the class or class-method or function
	Maybe the NamedTypeList with better name should be the place for this 
	it represent a name (param or property) or variable name?
	The execution context could hold docs?
	/*public function setTypeHint($hint) {
		if ($hint != null)
			$this->parameterTypeHint = "T[" . $hint->getName() . "]";
	}*/

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

	protected function getCommentOn(VariableName $variableName) {

		$lines = explode("\n", $this->documentation);

		$ret = "";
		foreach ($lines as $line) {
//			if(strpos($line, "$". $variableName->toString()) !== FALSE) {
				$ret .= "C[" . trim($line) . "]";
//			}
		}

		return $ret;
	}
}