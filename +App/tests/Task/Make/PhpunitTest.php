<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Make_Phpunit;

class Task_Make_PhpunitTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Make_Phpunit'));
	}

	// @todo tests for \mjolnir\cfs\Task_Make_Phpunit

} # test
