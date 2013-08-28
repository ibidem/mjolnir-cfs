<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Find_Config;

class Task_Find_ConfigTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Find_Config'));
	}

	// @todo tests for \mjolnir\cfs\Task_Find_Config

} # test
