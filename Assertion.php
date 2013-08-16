<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Assertion extends \app\Instantiatable
{
	/**
	 * @var mixed
	 */
	protected $asserted;

	/**
	 * @return static
	 */
	static function of($asserted)
	{
		$i = static::instance();
		$i->asserted = $asserted;

		return $i;
	}

	function equals($value)
	{
		if ($this->asserted === $value)
		{
			throw new \app\Exception("Expected [${$this->asserted}] recieved [${$value}]");
		}
	}

} # class
