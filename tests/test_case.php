<?php
/*
 * This is a testcase for using the profiler
 */

	//To initialize the profiler
	require_once("../NameExtractor.php");
	$profiler = new \analyser\NameExtractor();
	$profiler->start();
	
	declare(ticks=1);
	//end of init
	/**
	* @param String $rawFunctionParameter4 A string
	*/
	function rawFunction($rawFunctionParameter4) {
		echo "$rawFunctionParameter4";
	}

	rawFunction("funky");


	//
	$profiler->stop();
?>