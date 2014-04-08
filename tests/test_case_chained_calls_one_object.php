<?php


class AClass {}

	//end of init
	/**
	* @param AClass $parameter An AClass instance
	*/
	function function1(AClass $parameter) {
		function2($parameter);
	}

	/**
	* @param AClass $parameter An AClass instance
	*/
	function function2(AClass $parameter) {
		echo "in Method";
	}

	$object = new AClass();
	function1($object );



	function assert_chained_calls_one_object(\analyser\NameExtractor $extractor) {
		
		$contexts = $extractor->getContexts();
		assertVariable($contexts, "function2()", "parameter", "AClass", array("AClass"));
		assertVariable($contexts, "function1()", "parameter", "AClass", array("AClass"));
	}