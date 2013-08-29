<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\CFS;

class CFSTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\CFS'));
	}

	// @todo tests for \mjolnir\cfs\CFS

} # test
