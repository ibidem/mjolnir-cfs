<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Task_Find_File;

class Task_Find_FileTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Task_Find_File'));
	}

	// @todo tests for \mjolnir\cfs\Task_Find_File

} # test
