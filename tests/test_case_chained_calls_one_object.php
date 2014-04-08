<?php


class AClass {}

	//end of init
	/**
	* @param AClass $parameter An AClass instance
	*/
	function function1(AClass $parameter) {
		function2($parameter);
	}

	/**
	* @param AClass $parameter An AClass instance
	*/
	function function2(AClass $parameter) {
		echo "in Method";
	}

	$object = new AClass();
	function1($object );




	
?>