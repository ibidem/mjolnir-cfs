<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Compile extends \app\Task_Base
{
	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$local = $this->get('local', false);

		if ( ! $local)
		{
			$paths = \app\CFS::paths();
		}
		else # only local files
		{
			$paths = [];
		}

		$env_config = \app\Env::key('environment.config');

		if (isset($env_config['themes']))
		{
			foreach ($env_config['themes'] as $theme => $path)
			{
				$paths[] = $path;
			}
		}

		$files = \app\CFS::find_files('#^\+compile.rb$#', $paths);

		if (empty($files))
		{
			$this->writer->writef(' No [+compile.rb] files detected on the system.')
				->eol()->eol();
		}

		foreach ($files as $file)
		{
			$file = \str_replace('\\', '/', $file);
			$this->writer->writef(' Running: '.$file)->eol()->eol();
			\passthru($file);
			$this->writer->eol()->eol();
		}
	}

} # class
