phpNameAnalyser
===============

Analyser for PHP variable names, dynamically extracts variable names and types and produces variable_names.serialized och variable_names.serialized.csv for further analysis.

	//To initialize the profiler
	require_once("NameExtractor.php");
	$profiler = new \analyser\NameExtractor();
	$profiler->start();
	
	declare(ticks=1);
	//end of init

	//analyse unit

	//finish and save results
	$profiler->stop();