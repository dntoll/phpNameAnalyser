<?php
/*
 * This is a testcase for using the profiler
 */

	
	//end of init


	//test program starts
	class SomeClass {
		/**
		* @var String
		*/
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
		 * @param  SomeClass $parameter2SomeClass [description]
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

		function doOtherFunkyShit(array $parameter3ArrayWithTypeHint) {
			$foo = "hello";
			for ($x = 0; $x < $parameter3ArrayWithTypeHint[0]; ++$x) {
		         echo "$this->property1String \n";
		   }
		}
	}

	class SubClass extends SomeClass {
		private $property4String = "Daniel";
	}

	
	$obj = new SomeClass();
	$obj->doSomeFunkyShit(1, $obj);
	$obj->doSomeFunkyShit(1, new Subclass());
	

	
?>