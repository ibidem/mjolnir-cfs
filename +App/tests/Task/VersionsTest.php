<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Versions;

class Task_VersionsTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Versions'));
	}

	// @todo tests for \mjolnir\cfs\Task_Versions

} # test
