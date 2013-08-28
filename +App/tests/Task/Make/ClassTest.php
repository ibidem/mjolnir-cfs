<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Make_Class;

class Task_Make_ClassTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Make_Class'));
	}

	// @todo tests for \mjolnir\cfs\Task_Make_Class

} # test
