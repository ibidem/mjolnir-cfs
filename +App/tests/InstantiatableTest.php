<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Instantiatable;

class TheFakeInstantiatableClass extends Instantiatable {}
class AnotherFakeInstantiatableClass extends Instantiatable {}

class InstantiatableTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_faked()
	{
		Instantiatable::fakeclass('mjolnir\cfs\tests\TheFakeInstantiatableClass');
		$i = Instantiatable::instance();
		$this->assertInstanceOf('mjolnir\cfs\tests\TheFakeInstantiatableClass', $i);
	}

	/** @test @depends can_be_faked */ function
	can_clear_up_fakes()
	{
		Instantiatable::fakeclass(null);
		$i = Instantiatable::instance();
		$this->assertInstanceOf('mjolnir\cfs\Instantiatable', $i);
	}

	/** @test @depends can_clear_up_fakes */ function
	fakes_dont_conflict_with_each_other()
	{
		TheFakeInstantiatableClass::fakeclass('mjolnir\cfs\tests\AnotherFakeInstantiatableClass');
		$i = Instantiatable::instance();
		$this->assertInstanceOf('mjolnir\cfs\Instantiatable', $i);
		$this->assertNotInstanceOf('mjolnir\cfs\tests\AnotherFakeInstantiatableClass', $i);
	}

} # test
