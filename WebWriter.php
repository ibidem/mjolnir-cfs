<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class WebWriter extends \app\Instantiatable implements \mjolnir\types\Writer
{
	use \app\Trait_Writer;

	/**
	 * @return static $this
	 */
	function eol()
	{
		echo $this->eolstring();
		return $this;
	}

	/**
	 * @return static $this
	 */
	function writef($format)
	{
		$args = \func_get_args();
		echo \call_user_func_array('sprintf', $args);
		return $this;
	}

	/**
	 * @return static $this
	 */
	function stderr_writef($format)
	{
		$args = \func_get_args();
		echo \call_user_func_array('sprintf', $args);
		return $this;
	}

	/**
	 * @return resource
	 */
	function stdout()
	{
		return null;
	}

} # class
