<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Find_Class;

class Task_Find_ClassTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Find_Class'));
	}

	// @todo tests for \mjolnir\cfs\Task_Find_Class

} # test
