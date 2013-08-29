<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Backend_FilePermissions;

class Backend_FilePermissionsTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\cfs\Backend_FilePermissions'));
	}

	// @todo tests for \mjolnir\cfs\Backend_FilePermissions

} # test
