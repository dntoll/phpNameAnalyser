<?php
/*
 * This is a testcase for using the extractor
 */
	
	//end of init
	/**
	* @param Exception $exception An Exception
	*/
	function function1(Exception $exception) {
		function2($exception);
	}

	function function2(Exception $exception) {
		echo "in Method";
	}

	$object = new Exception();
	function1($object );


	
?>