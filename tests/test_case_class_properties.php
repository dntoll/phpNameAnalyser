<?php

class ThreeProperties {
	/**
	* @var string
	*/
	private $property1String = "Daniel";
	private $property2Object = null;
	private $property3Array = null;

	function __construct() {
		$localVariable = "localVariable";
		$this->property1String = "Daniel foo";
		$this->property3Array  = array();
		$this->property3Array[]= 0;
		$this->property3Array[]= "foo";
		$this->property2Object = new \Exception();
	}
}

new ThreeProperties();