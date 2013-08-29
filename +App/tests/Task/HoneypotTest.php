<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Honeypot;

class Task_HoneypotTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Honeypot'));
	}

	// @todo tests for \mjolnir\cfs\Task_Honeypot

} # test
