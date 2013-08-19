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
	private $_asserted_object;

	/**
	 * @return \app\Assertion|static
	 */
	static function that($subject)
	{
		if (\is_object($subject))
		{
			$i = static::instance();
			$i->_asserted_object = $subject;
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
		if (\is_callable([$this->_asserted_object, $name]) && \method_exists($this->_asserted_object, $name))
		{
			$method_result = \call_user_func_array([$this->_asserted_object, $name], $arguments);
			return \app\Assertion::of($method_result);
		}
		else # uncallable || no such method
		{
			if (\is_callable([$this->_asserted_object, $name]))
			{
				$classname = \get_class($this->_asserted_object);
				throw new \app\Exception("The class [$classname] does not have a method [$name].");
			}
			else # not callable
			{
				if (\is_object($this->_asserted_object))
				{
					throw new \app\Exception("Can not call method [$name] on object.");
				}
				else # non-object
				{
					throw new \app\Exception("Calling method [$name] on non-object.");
				}
			}
		}

	}

} # class
