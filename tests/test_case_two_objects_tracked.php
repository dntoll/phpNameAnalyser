<?php

	class BaseClass {
		public $property = 3;
	}

	class SubClass extends BaseClass {
		
	}
	function twoObjectsPassThrough1(BaseClass $base) {
		twoObjectsPassThrough2($base);
	}

	function twoObjectsPassThrough2(BaseClass $parameterName) {
		var_dump($parameterName);
		echo "<br/>";
		if (true)
			echo 6;
	}


	$object = new BaseClass();
	twoObjectsPassThrough1($object );
	$object = new SubClass();
	twoObjectsPassThrough1($object );

	
	
