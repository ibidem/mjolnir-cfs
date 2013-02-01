<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Compile extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$paths = \app\CFS::paths();
		$env_config = include ENVFILE;

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
			$this->writer->writef(' Running: '.$file)->eol()->eol();
			\passthru($file);
			$this->writer->eol()->eol();
		}
	}

} # class
