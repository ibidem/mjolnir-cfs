<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Config;

class Task_ConfigTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Config'));
	}

	// @todo tests for \mjolnir\cfs\Task_Config

} # test
