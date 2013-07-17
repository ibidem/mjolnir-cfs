<?php namespace mjolnir\cfs;

/**
 * This class serves only to gurantee a class is implementing instantiatable and
 * also avoid any errors in using instantiatable, ie. calls to the constructor.
 *
 * It is not necesary to extend this class when implementing the interface, but
 * it is recomended since it facilitates certain operations.
 *
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Instantiatable implements \mjolnir\types\Instantiatable
{
	use \app\Trait_Instantiatable;

	/**
	 * @var array
	 */
	private static $classfakes = null;

	/**
	 * Private constructor to deny access to it.
	 */
	private function __construct()
	{
		// disabled
	}

	/**
	 * @see \mjolnir\types\Instantiatable
	 * @return static
	 */
	static function instance()
	{
		if (self::$classfakes === null)
		{
			return new static;
		}
		else # check class fakeups
		{
			if (isset(self::$classfakes[\get_called_class()]))
			{
				return new self::$classfakes[\get_called_class()];
			}
			else # we don't have any fake for this class
			{
				return new static;
			}
		}
	}

	/**
	 * This method is easier to explain via an example,
	 *
	 *		\app\SomeClass::fakeclass('\example\SomeOtherClass');
	 *		\app\SomeClass::instance(); # instance of \example\SomeOtherClass
	 *
	 *		\app\SomeClass::fakeclass(null);
	 *		\app\SomeClass::instance(); # instance of \app\SomeClass
	 *
	 * This method is useful in tests. Note that most static interfaces are
	 * merely aliases to non-static interfaces.
	 *
	 * [!!] This method is intentionally not part of the interface.
	 */
	static function fakeclass($class)
	{
		if ($class !== null)
		{
			self::$classfakes[\get_called_class()] = $class;
		}
		else # remove fake
		{
			unset(self::$classfakes[\get_called_class()]);

			if (empty(self::$classfakes))
			{
				self::$classfakes = null;
			}
		}
	}

} # class
