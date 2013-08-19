<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;
use app\Assert;

\mjolnir\cfs\Mjolnir::behat();

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param array $parameters context parameters (set them up through behat.yml)
	 */
	function __construct(array $parameters)
	{
		// do nothing
	}

	/**
	 * @Given /^a class file "([^"]*)" with a method hello_world\.$/
	 */
	function aClassFileWithAMethodHelloWorld($class_file)
	{
		$class_exists = \file_exists(\app\CFS::modulepath('mjolnir\testing').$class_file.EXT);
		Assert::that($class_exists)->equals(true);
	}

	/**
	 * @When /^I call "([^"]*)" and invoke "([^"]*)" I should get "hello, world"\.$/
	 */
	function iCallAndInvokeIShouldGetHelloWorld($class, $method)
	{
		Assert::that($class::instance())->$method()->equals('hello, world');
	}

} # context
