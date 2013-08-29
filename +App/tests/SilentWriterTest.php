<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\SilentWriter;

class SilentWriterTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\SilentWriter'));
	}

	// @todo tests for \mjolnir\cfs\SilentWriter

} # test
