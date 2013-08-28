<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Make_Module;

class Task_Make_ModuleTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Make_Module'));
	}

	// @todo tests for \mjolnir\cfs\Task_Make_Module

} # test
