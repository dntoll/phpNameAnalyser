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

	public function __construct(VariableName $name, Comment $comment) {
		$this->name = $name;
		$this->comment = $comment;
	}

	public function addType(Type $type, $arrayTypes) {
		$this->types[$type->toString()] = $arrayTypes;
	}

	public function setTypeHint($typeHint) {
		$this->typeHint = $typeHint;
	}

	public function getTypes() {
		return $this->types;
	}

	public function getName() {
		return $this->name;
	}

	public function getTypeHint() {
		if ($this->typeHint != null)
			return $this->typeHint->getName();
		else
			return "";
	}

	public function getCommentOn() {
		return $this->comment;
	}
}