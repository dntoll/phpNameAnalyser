<?php

	//test program starts
	class ArrayParameter {
		
		function methodWithArrayParameter(array $parameter3ArrayWithTypeHint) {
			echo "foo";
		}
	}

		
	$obj = new ArrayParameter();
	$obj->methodWithArrayParameter(array(1, "Daniel", new Exception()));
	
?>