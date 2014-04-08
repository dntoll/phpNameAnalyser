<?php

	//test program starts
	class ArrayParameter {
		
		function methodWithArrayParameter(array $parameter3ArrayWithTypeHint) {
			echo "foo";
		}
	}

		
	$obj = new ArrayParameter();
	$obj->methodWithArrayParameter(array(1, "Daniel", new Exception()));
	
	function assert_array_parameter(\analyser\NameExtractor $extractor) {
		
		$contexts = $extractor->getContexts();
		assertVariable($contexts, "ArrayParameter.methodWithArrayParameter()", "parameter3ArrayWithTypeHint", "array", array("array"));
	
	}