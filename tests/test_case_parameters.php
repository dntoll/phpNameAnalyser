<?php


class ExampleParameterClass {}

	

//test program starts
class ParameterTestClass {
	
	function __construct() {
	}

	/**
	 * [doSomeFunkyShit description]
	 * @param  Integer    $parameter1Integer          [description]
	 * @param  ExampleParameterClass $parameter2SomeClass [description]
	 * @return [type]                            [description]
	 */
	function methodWith2Parameters($parameter1Integer, ExampleParameterClass $parameter2SomeClass) {
		echo "method called";
	}

	
}


$obj = new ParameterTestClass();
$obj->methodWith2Parameters(1, new ExampleParameterClass());
	

function assert_parameters(\analyser\NameExtractor $extractor) {
		
	$contexts = $extractor->getContexts();
	assertVariable($contexts, "ParameterTestClass.methodWith2Parameters()", "parameter1Integer", "", array("integer"));
	assertVariable($contexts, "ParameterTestClass.methodWith2Parameters()", "parameter2SomeClass", "ExampleParameterClass", array("ExampleParameterClass"));
	
}