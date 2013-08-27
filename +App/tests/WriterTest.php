<?php namespace mjolnir\cfs;

class WriterTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_instantiated()
	{
		\mjolnir\cfs\Writer::instance();
	}

} # test
