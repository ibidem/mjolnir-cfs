<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Overlord;

class OverlordTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Overlord'));
	}

	// @todo tests for \mjolnir\cfs\Overlord

} # test
