<?php
/*
 * from http://www.php.net/manual/en/internals2.opcodes.ticks.php
 * opcode number: 105
 */
// A function that records the time when it is called


require_once("profiler.php");
$profiler = new \profiler\Profiler();
register_tick_function(array(&$profiler, 'tick'), true);
declare(ticks=1);

class SomeClass {
	private $thatString = "Daniel";
	private $thatObject = null;

	function doSomeFunkyShit($iterations, SomeClass $test) {
		$this->thatObject = $test;
		$test->doOtherFunkyShit($iterations + 1);
	}

	function doOtherFunkyShit($iterations) {
		$foo = "hello";
		for ($x = 0; $x < $iterations; ++$x) {
	         echo "$this->thatString \n";
	   }
	}
}

class SubClass extends SomeClass {

}

// Set up a tick handler


// Initialize the function before the declare block
//profile();

// Run a block of code, throw a tick every 2nd statement
	$obj = new SomeClass();
	$obj->doSomeFunkyShit(1, $obj);
	$obj->doSomeFunkyShit(1, new Subclass());


unregister_tick_function(array(&$profiler, 'tick'));
$profiler->show();
?>