<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Mjolnir;

class MjolnirTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Mjolnir'));
	}

	// @todo tests for \mjolnir\cfs\Mjolnir

} # test
