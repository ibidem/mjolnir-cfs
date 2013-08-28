<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Licenses;

class Task_LicensesTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Licenses'));
	}

	// @todo tests for \mjolnir\cfs\Task_Licenses

} # test
