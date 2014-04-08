<?php


/*
namespace analyser;

class Scope {

	
	private $name;
	private static $property = null;
	private static $parameter = null;

	private function __construct( $type) {
		if (is_string($type) == false) {
			throw new \Exception("Should be string");
		}
		$this->name = $type;
	}

	public static function getParameterScope() {
		if (self::$parameter == null) {
			self::$parameter = new Scope("Parameter");
		}

		return self::$parameter;
	}

	public static function getPropertyScope() {
		if (self::$property == null) {
			self::$property = new Scope("Property");
		}

		return self::$property;
	}

	public function toString() {
		return $this->name;
	}
}*/