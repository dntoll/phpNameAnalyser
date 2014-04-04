<?php

class Test {

}
$a = new Test();

$b = $a;

$c = new Test();

	
	var_dump($a);
	xdebug_var_dump($b);
	xdebug_var_dump($c);
