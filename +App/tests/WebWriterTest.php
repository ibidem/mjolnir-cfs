<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\WebWriter;

class WebWriterTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\WebWriter'));
	}

	// @todo tests for \mjolnir\cfs\WebWriter

} # test
