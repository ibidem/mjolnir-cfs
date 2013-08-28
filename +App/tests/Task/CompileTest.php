<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Compile;

class Task_CompileTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Compile'));
	}

	// @todo tests for \mjolnir\cfs\Task_Compile

} # test
