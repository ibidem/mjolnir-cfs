<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Benchmark;

class BenchmarkTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Benchmark'));
	}

	// @todo tests for \mjolnir\cfs\Benchmark

} # test
