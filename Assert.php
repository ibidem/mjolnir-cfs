<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Assert extends \app\Instantiatable
{
	/**
	 * @var mixed
	 */
	private $__asserted_object;

	/**
	 * @return \app\Assertion|static
	 */
	static function that($subject)
	{
		if (\is_object($subject))
		{
			$i = static::instance()
			$i->__asserted_object = $subject;
			return $i;
		}
		else # non-object
		{
			return \app\Assertion::of($subject);
		}
	}

	/**
	 * ...
	 */
	function __call($name, $arguments)
	{
		$method_result = \call_user_function([$this->__asserted_object, $name], $arguments)
		return \app\Assertion::of($method_result);
	}

} # class
