<?php


class MyBaseClass {}

class MySubClass extends MyBaseClass {}
	

function test_case_find_base_classes(MySubClass $myBaseClassName) {
	echo "in Method";
}

test_case_find_base_classes(new MySubClass());


function assert_object_parent_classes(\analyser\NameExtractor $extractor) {

	$contexts = $extractor->getContexts();

	assertVariable($contexts, "test_case_find_base_classes()", "myBaseClassName", "MySubClass", array("MySubClass"), array("MyBaseClass"));
	
}