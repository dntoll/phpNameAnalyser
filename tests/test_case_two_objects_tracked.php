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
		//var_dump($parameterName);
		echo "<br/>";
		if (true)
			echo 6;
	}


	$object = new BaseClass();
	twoObjectsPassThrough1($object );
	$object = new SubClass();
	twoObjectsPassThrough1($object );

	function assert_two_objects_tracked(\analyser\NameExtractor $extractor) {
		
		$contexts = $extractor->getContexts();
		assertVariable($contexts, "twoObjectsPassThrough1()", "base", "BaseClass", array("BaseClass", "SubClass"));
		assertVariable($contexts, "twoObjectsPassThrough2()", "parameterName", "BaseClass", array("BaseClass", "SubClass"));

		//var_dump($extractor->getObjects());

		assert(count($extractor->getObjects()) == 2);
		foreach ($extractor->getObjects() as $key => $object) {
			assert(count($object) == 2); //två metoder
		}
	}
	
