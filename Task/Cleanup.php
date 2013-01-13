<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Cleanup extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);
		
		$this->writer->writef(' Reseting cache.')->eol();
		\app\Stash::flush();
		$this->writer->writef(' Removing logs.')->eol();
		\app\Filesystem::purge(APPPATH.'logs');
		$this->writer->writef(' Removing cache files.')->eol();
		\app\Filesystem::purge(APPPATH.'cache');
		$this->writer->writef(' Removing temporary files.')->eol();
		\app\Filesystem::purge(APPPATH.'tmp');
	}

} # class
