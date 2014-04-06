<?php


require_once("../phpLoggerLib/Logger.php");
require_once("NameExtractor.php");

loggThis("Starting tests");
	
if ($handle = opendir('tests')) {
	while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {

        	if (strpos($entry, "test_") !== FALSE) {

                declare(ticks=1);
                loggHeader("$entry");
        		$profiler = new \analyser\NameExtractor(false, "data/$entry");
				$profiler->start();
				
				
            	require_once("tests/" . $entry);

            	$profiler->stop();
            }
        }
    }
    closedir($handle);

	loggThis("Done running tests");


	
} else {
	throw new Exception("unable to find tests");
}

dumpLog(false);