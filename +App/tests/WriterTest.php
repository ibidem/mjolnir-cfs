<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Writer;

class WriterTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_instantiated()
	{
		$i = Writer::instance();
		$this->assertInstanceOf('mjolnir\cfs\Writer', $i);
	}

} # test
