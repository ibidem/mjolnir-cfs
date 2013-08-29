<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Behat;

class Task_BehatTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Behat'));
	}

	// @todo tests for \mjolnir\cfs\Task_Behat

} # test
