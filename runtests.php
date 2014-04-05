<?php

require_once("NameExtractor.php");
	
if ($handle = opendir('tests')) {
	while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {

        	if (strpos($entry, "test_") !== FALSE) {
        		$profiler = new \analyser\NameExtractor(false, "data/$entry");
				$profiler->start();
				
				declare(ticks=1);
            	echo "$entry\n";
            	require_once("tests/" . $entry);

            	$profiler->stop();
            }
        }
    }
    closedir($handle);

	


	
} else {
	throw new Exception("unable to find tests");
}