<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Find_File extends \app\Task_Base
{
	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$files = \app\CFS::file_list($this->get('path', false), $this->get('ext', EXT));

		if ( ! empty($files))
		{
			\sort($files);
			foreach ($files as $file)
			{
				$this->writer->printf('status', 'File', \str_replace(\str_replace('\\', '/', \app\Env::key('sys.path')), '', \str_replace('\\', '/', \realpath($file))))->eol();
			}
		}
		else # no files found
		{
			$this->writer->printf('error', 'No files found.')->eol();
		}
	}

} # class
