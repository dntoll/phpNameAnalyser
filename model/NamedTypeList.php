<?php

namespace analyser;

class NamedTypeList {

	/**
	 * @var String
	 */
	private $name;
	private $types = array();

	public function __construct(VariableName $name) {
		$this->name = $name;
	}

	public function addType(Type $type, $arrayTypes) {
		$this->types[$type->toString()] = $arrayTypes;
	}

	public function getTypes() {
		return $this->types;
	}
}