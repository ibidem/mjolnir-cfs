<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Writer extends \app\Instantiatable implements \mjolnir\types\Writer
{
	use \app\Trait_Writer;

	/**
	 * @return \mjolnir\cfs\Writer_Task
	 */
	static function instance()
	{
		$instance = parent::instance();

		$instance->stdout_is(\fopen('php://stdout', 'w'));
		$instance->stderr_is(\fopen('php://stderr', 'w'));

		return $instance;
	}

} # class
