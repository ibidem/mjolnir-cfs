<?php namespace app;

/**
 * Custom Instantiation class for testing purposes.
 */
class Instantiatable extends \mjolnir\Instantiatable
	implements \mjolnir\types\Instantiatable
{
	/**
	 * @var array
	 */
	protected static $test_redirects = [];

	/**
	 * This function can be used to redirect all instances of a class to a
	 * mockup version for testing purposes.
	 *
	 * @param string from class
	 * @param string to class
	 */
	static function test_redirect($from_class, $to_class)
	{
		static::$test_redirects[$from_class] = $to_class;
	}

	/**
	 * This function can be used to redirect all instances of a class to a
	 * mockup version for testing purposes.
	 *
	 * @param string from class
	 * @param string to class
	 */
	static function test_redirects(array $redirects)
	{
		static::$test_redirects = $redirects;
	}

	/**
	 * @return \app\Instantiatable
	 */
	static function instance()
    {
        $class = \get_called_class();
        if (isset(static::$test_redirects[$class]))
        {
            $class = static::$test_redirects[$class];
            return $class::instance();
        }

        return parent::instance();
    }

	/**
	 * Reset all class redirects so any tests that don't need the mockups don't
	 * get corrupted.
	 */
	static function test_reset()
	{
		static::$test_redirects = array();
	}

} # class
