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
		private $property1String = "Daniel";
		private $property2Object = null;
		private $property3Array = null;

		function __construct() {
			$localVariable = "localVariable";
			$this->property1String = "Daniel foo";
			$this->property3Array  = array();
			$this->property3Array[]= 0;
			$this->property3Array[]= "foo";
		}

		/**
		 * [doSomeFunkyShit description]
		 * @param  Integer    $parameter1Integer          [description]
		 * @param  SomeClass $thatSomeClassParameter [description]
		 * @return [type]                            [description]
		 */
		function doSomeFunkyShit($parameter1Integer, SomeClass $parameter2SomeClass) {
			$this->property2Object = $parameter2SomeClass;

			$array = array();
			$array[] = $parameter1Integer + 1;
			
			$parameter2SomeClass->doOtherFunkyShit($array);
			$array[] = $parameter2SomeClass;
			$parameter2SomeClass->doOtherFunkyShit($array);
		}

		function doOtherFunkyShit($parameter3SomeClass) {
			$foo = "hello";
			for ($x = 0; $x < $parameter3SomeClass[0]; ++$x) {
		         echo "$this->property1String \n";
		   }
		}
	}

	class SubClass extends SomeClass {
		private $thatSubClassPropertyString = "Daniel";
	}

	function rawFunction($rawFunctionParameter4) {
		echo "$rawFunctionParameter4";
	}

	$obj = new SomeClass();
	$obj->doSomeFunkyShit(1, $obj);
	$obj->doSomeFunkyShit(1, new Subclass());
	rawFunction("funky");

	$profiler->stop();
?>