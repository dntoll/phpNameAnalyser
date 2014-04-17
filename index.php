<?php
/*
 * This is a testcase for using the profiler
 */

	//To initialize the profiler
	require_once("NameExtractor.php");
	$profiler = new \analyser\NameExtractor();
	$profiler->start();
	
	declare(ticks=1);
	//end of init


	//test program starts
	class SomeClass {
		private $thatString = "Daniel";
		private $thatObject = null;

		function doSomeFunkyShit($thatParameter, SomeClass $thatSomeClassParameter) {
			$this->thatObject = $thatSomeClassParameter;

			$array = array();
			$array[] = $thatParameter + 1;
			$array[] = $thatSomeClassParameter;
			$thatSomeClassParameter->doOtherFunkyShit($array);
		}

		function doOtherFunkyShit($thatArrayParameter) {
			$foo = "hello";
			for ($x = 0; $x < $thatArrayParameter[0]; ++$x) {
		         echo "$this->thatString \n";
		   }
		}
	}

	class SubClass extends SomeClass {

	}

	function rawFunction($rawFunctionParameter) {
		echo "$rawFunctionParameter";
	}

	$obj = new SomeClass();
	$obj->doSomeFunkyShit(1, $obj);
	$obj->doSomeFunkyShit(1, new Subclass());
	rawFunction("funky");

	$profiler->stop();
?>