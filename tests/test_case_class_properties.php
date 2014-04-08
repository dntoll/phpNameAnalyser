<?php

class ExampleClass {}

class ThreeProperties {
	/**
	 * A property with comment 
	 * @var string
	 */
	private $property_1_StringWithComment = "Daniel";
	private $property_2_ExampleClass = null;
	private $property_3_Array = null;

	function __construct() {
		$localVariable = "localVariable";
		$this->property_3_Array  = array();
		$this->property_3_Array[]= 0;
		$this->property_3_Array[]= "foo";
		$this->property_2_ExampleClass = new \ExampleClass();
		$this->property_3_Array[]= $this->property_2_ExampleClass;
	}
}

new ThreeProperties();

	function assert_class_properties(\analyser\NameExtractor $extractor) {
		
		$contexts = $extractor->getContexts();
		$str = $contexts["ThreeProperties"]->getDeclarations();
		//var_dump($str['property_1_StringWithComment']);
		assertVariable($contexts, "ThreeProperties", "property_1_StringWithComment", "", array("string"));
		assertVariable($contexts, "ThreeProperties", "property_2_ExampleClass", "", array("ExampleClass"));
		assertVariable($contexts, "ThreeProperties", "property_3_Array", "", array("array"));
	}