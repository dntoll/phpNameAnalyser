<?php

namespace analyser;

class VariableDeclaration {

	/**
	 * @var VariableName
	 */
	private $name;
	private $types = array();
	private $typeHint;
	private $comment;
	private $context;

	public function __construct(VariableName $name, Comment $comment, AbstractExecutionContext $context) {
		$this->name = $name;
		$this->comment = $comment;
		$this->context = $context;
	}

	public function addType(Type $type, $arrayTypes) {
		$this->types[$type->toString()] = $arrayTypes;
	}

	public function setTypeHint($typeHintString) {
		$this->typeHint = $typeHintString;

	}

	public function getTypes() {
		return $this->types;
	}

	public function getContextFunction() {
		return $this->context->getFunction();
	}

	public function getName() {
		return $this->name;
	}

	public function getTypeHint() {
		if ($this->typeHint != null)
			return $this->typeHint;
		else
			return "";
	}

	public function getComment() {
		return $this->comment;
	}
}