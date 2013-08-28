<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Assertion;

class AssertionTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Assertion'));
	}

	// @todo tests for \mjolnir\cfs\Assertion

} # test
