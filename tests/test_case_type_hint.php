<?php
/*
 * This is a testcase for using the extractor
 * This particular test does not work
 */
	

class TypeHintClass {}
//end of init
/**
* @param array $typeHintedParameter An array
*/
function functionWithOneParam(TypeHintClass $typeHintedParameter) {
	echo "in Method";
}

functionWithOneParam(new TypeHintClass());

function assert_type_hint(\analyser\NameExtractor $extractor) {

	$contexts = $extractor->getContexts();
	assertVariable($contexts, "functionWithOneParam()", "typeHintedParameter", "TypeHintClass", array("TypeHintClass"));
}