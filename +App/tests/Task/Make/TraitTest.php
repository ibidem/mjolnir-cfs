<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Make_Trait;

class Task_Make_TraitTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Make_Trait'));
	}

	// @todo tests for \mjolnir\cfs\Task_Make_Trait

} # test
