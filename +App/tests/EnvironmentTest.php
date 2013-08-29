<?php namespace mjolnir\cfs\tests;

use \mjolnir\cfs\Environment;

class EnvironmentTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_instantiated()
	{
		$i = Environment::instance();
		$this->assertInstanceOf('mjolnir\cfs\Environment', $i);
	}

	/** @test */ function
	can_instantiate_testcase_environment()
	{
		$i = Environment::instance('testcase');
		$this->assertInstanceOf('mjolnir\cfs\Environment', $i);
	}

	/** @test */ function
	retrieving_a_nonexistent_key_returns_the_default()
	{
		$i = Environment::instance('testcase');

		$result = $i->key('a_key');
		$this->assertNull($result);

		$result = $i->key('a_key', 'custom_default');
		$this->assertEquals($result, 'custom_default');
	}

	/** @test */ function
	keys_may_be_set()
	{
		$i = Environment::instance('testcase');
		$i->set('a_key', 'custom_value');
	}

	/** @test @depends keys_may_be_set */ function
	key_value_may_be_retrieved()
	{
		$i = Environment::instance('testcase');
		$this->assertEquals($i->key('a_key'), 'custom_value');
	}

	/** @test @depends key_value_may_be_retrieved */ function
	keys_can_be_ensured()
	{
		$i = Environment::instance('testcase');

		// ensured existing keys maintain their existing value
		$i->ensure('a_key', 'a_value');
		$this->assertEquals($i->key('a_key'), 'custom_value');

		// ensured nonexistent keys are set
		$this->assertEquals($i->key('another_key', null), null);
		$i->ensure('another_key', 'a_value');
		$this->assertEquals($i->key('another_key'), 'a_value');
	}

} # test
