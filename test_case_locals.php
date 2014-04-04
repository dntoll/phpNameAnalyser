<?php

	/*
	 * This is a testcase for using the profiler
	 */

	//To initialize the profiler
	require_once("NameExtractor.php");
	$profiler = new \analyser\NameExtractor();
	$profiler->start();
	
	declare(ticks=1);


	//Analyse
	
	function someFunction($slart) {
		$localVariable = "localValue";
		echo $localVariable;
	}	

	someFunction(1);

	//Done
	$profiler->stop();
