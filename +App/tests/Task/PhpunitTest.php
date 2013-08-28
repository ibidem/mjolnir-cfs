<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Phpunit;

class Task_PhpunitTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Phpunit'));
	}

	// @todo tests for \mjolnir\cfs\Task_Phpunit

} # test
