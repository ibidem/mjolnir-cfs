<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Cleanup;

class Task_CleanupTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Cleanup'));
	}

	// @todo tests for \mjolnir\cfs\Task_Cleanup

} # test
