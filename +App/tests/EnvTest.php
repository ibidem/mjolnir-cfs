<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Env;

class EnvTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Env'));
	}

	// @todo tests for \mjolnir\cfs\Env

} # test
