<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Log_Short;

class Task_Log_ShortTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Log_Short'));
	}

	// @todo tests for \mjolnir\cfs\Task_Log_Short

} # test
