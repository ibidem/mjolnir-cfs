<?php namespace mjolnir\cfs;

/**
 * The following class voids all the interface mehtods. It is used in Tasks
 * when they are invoked from outside a TaskRunner.
 *
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class SilentWriter extends \app\Instantiatable implements \mjolnir\types\Writer
{
	use \app\Trait_Writer;

	/**
	 * @return static $this
	 */
	function eol()
	{
		return $this;
	}

	/**
	 * @return static $this
	 */
	function writef($format)
	{
		return $this;
	}

	/**
	 * @return static $this
	 */
	function stderr_writef($format)
	{
		return $this;
	}

	/**
	 * Write using given format.
	 *
	 * @return static $this
	 */
	function printf($format)
	{
		return $this;
	}

} # class
