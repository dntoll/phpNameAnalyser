<?php

namespace analyser;

class Type {

	/**
	 * @var String
	 */
	private $name;

	public function __construct( $type) {
		if (is_string($type) == false) {
			throw new \Exception("Should be string");
		}
		$this->name = $type;
	}

	public function toString() {
		return $this->name;
	}
}