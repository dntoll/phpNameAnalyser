<?php

namespace analyser;



/**
 * The class and function
 */
class Comment {

	
	protected $documentation = "";

	public function __construct($docs) {
		if ($docs != null)
			$this->documentation = $docs;	
	}

	public function getCommentOn(VariableName $variableName) {

		$lines = explode("\n", $this->documentation);

		$ret = "";
		foreach ($lines as $line) {
			if(strpos($line, "$". $variableName->toString()) !== FALSE) {
				$ret .= "C[" . trim($line) . "]";
			}
		}

		return new Comment($ret);
	}

	public function toString() {

		$lines = explode("\n", $this->documentation);

		$ret = "";
		foreach ($lines as $line) {
//			if(strpos($line, "$". $variableName->toString()) !== FALSE) {
				$ret .= "C[" . trim($line) . "]";
//			}
		}

		return str_replace("\t", " ", $ret);

		
	}
}