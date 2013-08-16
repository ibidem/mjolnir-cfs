<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

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
    public function __construct(array $parameters)
    {
		// do nothing
	}

	/**
	 * @Given /^a class file "([^"]*)" with a method hello_world\.$/
	 */
	public function aClassFileWithAMethodHelloWorld($class_file)
	{
		\app\expects(true)->equals(\file_exists(PLGPATH.'mjolnir/testing/'.$class_file.EXT));
	}

	/**
	 * @When /^I call "([^"]*)" and invoke "([^"]*)" I should get "hello, world"\.$/
	 */
	public function iCallAndInvokeIShouldGetHelloWorld($class, $method)
	{
		\app\expects('hello, world')->equals($class::instance()->$method());
	}

} # context
