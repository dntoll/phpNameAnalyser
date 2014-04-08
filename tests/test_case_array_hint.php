<?php
/*
 * This is a testcase for using the extractor
 * This particular test does not work
 */
	
	//end of init
	/**
	* @param array $rawFunctionParameter4 An array
	*/
	function test_case_array_hint(array $arrayParameter) {
		echo "in Method";
	}

	test_case_array_hint(array("funky"));


	function assert_array_hint(\analyser\NameExtractor $extractor) {

		$contexts = $extractor->getContexts();

		assertVariable($contexts, "test_case_array_hint()", "arrayParameter", "array", array("array"));
		
	}