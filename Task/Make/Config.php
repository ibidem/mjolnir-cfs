<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Make_Config extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);
		
		$config = \app\CFS::config($this->get('config'));

		if (empty($config))
		{
			$this->writer
				->printf('status', 'Info', 'Configuration empty. No file created.')
				->eol();
		}
		else # configuration not empty
		{
			$config_file = APPPATH.\app\CFS::CNFDIR.DIRECTORY_SEPARATOR.$this->config['config'];
			if (\file_exists($config_file.EXT) && ! $this->config['forced'])
			{
				$this->writer
					->printf('error', 'File already exists. Use --forced to overwrite.')
					->eol();
			}
			else # file doesn't exist, or is forced
			{
				// windows handling
				if (DIRECTORY_SEPARATOR === '\\')
				{
					$config_file = \str_replace('/', '\\', $config_file);
				}
				// gurantee directory structure
				$dir = \substr($config_file, 0, \strrpos($config_file.EXT, DIRECTORY_SEPARATOR));
				if ( ! \is_dir($dir))
				{
					\mkdir($dir, 0777, true);
				}
				// dump contents
				\file_put_contents($config_file.EXT, '<?php return '.\var_export($config, true).';');
				// confirm
				$this->writer
					->printf('status', 'Info', 'Configuration file created.')
					->eol();
			}
		}
	}

} # class