<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Assert;

class AssertTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Assert'));
	}

	// @todo tests for \mjolnir\cfs\Assert

} # test
