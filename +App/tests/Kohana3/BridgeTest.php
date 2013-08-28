<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Kohana3_Bridge;

class Kohana3_BridgeTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Kohana3_Bridge'));
	}

	// @todo tests for \mjolnir\cfs\Kohana3_Bridge

} # test
