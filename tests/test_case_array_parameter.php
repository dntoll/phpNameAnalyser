<?php
/*
 * This is a testcase for using the profiler
 */

	
	//end of init


	//test program starts
	class ArrayParameter {
		
		function methodWithArrayParameter(array $parameter3ArrayWithTypeHint) {
			echo "foo";
		}
	}

		
	$obj = new ArrayParameter();
	$obj->methodWithArrayParameter(array(1, "dnaiel", new Exception()));
	
?>