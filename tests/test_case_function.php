<?php

	

	/**
	* @param String $documentedStringParameter A string parameter
	*/
	function functionWithOneStringParameter($documentedStringParameter) {
		echo "$documentedStringParameter";
	}

	functionWithOneStringParameter("funky");


	function assert_function(\analyser\NameExtractor $extractor) {
		
		$contexts = $extractor->getContexts();
		assertVariable($contexts, "functionWithOneStringParameter()", "documentedStringParameter", "", array("string"));
		
	}