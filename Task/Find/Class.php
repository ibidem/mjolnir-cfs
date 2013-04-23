<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Find_Class extends \app\Task_Base
{
	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$classfile = \str_replace('_', DIRECTORY_SEPARATOR, $this->get('class')).EXT;
		$modules = \array_keys(\app\CFS::system_modules());
		// search for class
		$files = array();
		foreach ($modules as $module)
		{
			if (\file_exists($module.DIRECTORY_SEPARATOR.$classfile))
			{
				$files[] = \str_replace(\app\Env::key('sys.path'), '', $module.'/'.$classfile);
			}
		}

		if ( ! empty($files))
		{
			\sort($files);
			foreach ($files as $file)
			{
				$this->writer->printf('status', 'File', $file)->eol();
			}
		}
		else # no files found
		{
			$this->writer->printf('error', 'No files found.')->eol();
		}
	}

} # class
