<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task;

class TaskTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task'));
	}

	// @todo tests for \mjolnir\cfs\Task

} # test
