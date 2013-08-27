<?php namespace mjolnir\cfs;

/**
 * Shorthand for \app\Environment::instance('main');
 *
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Env
{
	#
	# This class is a shorthand / alias to the object system.
	#
	# We do not ensure tests for this class since it merely mimicks the exact
	# same interface and tests for it are redundant and would duplicate the
	# logic in the instantiatable version.
	#

// @codeCoverageIgnoreStart

	/**
	 * @return mixed
	 */
	static function key($key, $default = null)
	{
		return \app\Environment::instance('main')->key($key, $default);
	}

	/**
	 * @return mixed
	 */
	static function ensure($key, $value)
	{
		return \app\Environment::instance('main')->ensure($key, $value);
	}

	/**
	 * @return static $this
	 */
	static function set($key, $value)
	{
		return \app\Environment::instance('main')->set($key, $value);
	}

// @codeCoverageIgnoreEnd

} # class
