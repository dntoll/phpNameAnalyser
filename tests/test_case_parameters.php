<?php
/*
 * This is a testcase for using the profiler
 */

	
	//end of init


	//test program starts
	class ParameterTestClass {
		
		function __construct() {
		}

		/**
		 * [doSomeFunkyShit description]
		 * @param  Integer    $parameter1Integer          [description]
		 * @param  Exception $parameter2SomeClass [description]
		 * @return [type]                            [description]
		 */
		function methodWith2Parameters($parameter1Integer, Exception $parameter2SomeClass) {
			echo "method called";
		}

		
	}

	
	$obj = new ParameterTestClass();
	$obj->methodWith2Parameters(1, new Exception());
	

	
?>