<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Status;

class Task_StatusTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Status'));
	}

	// @todo tests for \mjolnir\cfs\Task_Status

} # test
