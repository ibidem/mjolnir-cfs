<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Base;

class Task_BaseTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Base'));
	}

	// @todo tests for \mjolnir\cfs\Task_Base

} # test
