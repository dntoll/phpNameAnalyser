phpNameAnalyser
===============

Analyser for PHP variable names, dynamically extracts variable names and types and produces variable_names.serialized och variable_names.serialized.csv for further analysis.


The project depends on phpLoggerLib https://github.com/dntoll/phpLoggerLib


	<?php
	require_once("../phpNameAnalyser/NameExtractor.php");
	$extractor = new \analyser\NameExtractor(true, "data/wp_measure");
	$extractor->start();
	declare(ticks=1);
	//require the program to be analyzed
	require_once("index.php");

	$extractor->stop();