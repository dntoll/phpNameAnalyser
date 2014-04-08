<?php


require_once("../phpLoggerLib/Logger.php");
require_once("NameExtractor.php");

loggThis("Starting tests");


function assertVariable(array $contexts, $functionName, $parameterName, $parameterTypeHint, $expectedDynamicTypes, array $expectedSubtypeArray = NULL) {
    $functionContext = $contexts[$functionName];
    assert($functionContext->getFunction() == $functionName);
    $variables = $functionContext->getDeclarations();
    $parameter = $variables[$parameterName];
    assert($parameter->getName()->toString() == $parameterName);
    assert($parameter->getTypeHint() == $parameterTypeHint);


    if(isset($expectedDynamicTypes)) {
        $dynamicTypes = $parameter->getTypes();
        foreach ($expectedDynamicTypes as $expectedType) {
            $found = false;
            foreach ($dynamicTypes as $key => $type) {
                if ($expectedType == $key)
                    $found = true;
            }
            assert($found);
        }
    }
    if(isset($expectedSubtypeArray)) {
        $arrayOfSubTypes = $parameter->getTypes();

        //var_dump($arrayOfSubTypes);
       // var_dump($expectedSubtypeArray);
        foreach ($expectedSubtypeArray as $expectedType) {
            $found = false;
            foreach ($arrayOfSubTypes as $subtypes) {
                foreach ($subtypes as $subtype) {
                    if ($expectedType == $subtype)
                        $found = true;
                }
            }
            assert($found);
        }
    }
    
}
	
if ($handle = opendir('tests')) {
	while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {

            //can run single TC by 
            //runtests.php?test=test_case_array_hint.php
            if (isset($_GET["test"]) && $_GET["test"] != $entry)
                continue;

        	if (strpos($entry, "test_") !== FALSE) {

                declare(ticks=1);
                loggHeader("$entry");
        		$profiler = new \analyser\NameExtractor(false, "data/$entry");
				$profiler->start();
				
				
            	require_once("tests/" . $entry);

            	$profiler->stop();

                $noTest_ = substr($entry, 10); //remove "test_case_"
                $nodotPHP = substr($noTest_, 0, strlen($noTest_) - 4); //remove ".php"

                call_user_func("assert_" . $nodotPHP, $profiler);
            }
        }
    }
    closedir($handle);

	loggThis("Done running tests");


	
} else {
	throw new Exception("unable to find tests");
}

echoLog(false);