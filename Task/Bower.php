<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Bower extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$install = $this->get('install', false);
		$local = $this->get('local', false);

		if ($local)
		{
			$maindir = \app\Env::key('sys.path').'themes/';
		}
		else # global
		{
			$maindir = \app\Env::key('sys.path');
		}

		if ($install)
		{
			$files = \app\Filesystem::matchingfiles($maindir, '#^\.bowerrc$#');

			foreach ($files as $file)
			{
				// read bower config
				$config = \json_decode(\file_get_contents($file), true);
				if (isset($config['directory']))
				{
					$rootdir = \dirname($file);

					if (\file_exists($rootdir.'/component.json'))
					{
						$dir = $rootdir.'/'.\ltrim($config['directory'], '\\/');
						$this->writer->eol()->writef(" Purging ".\str_replace(\app\Env::key('sys.path'), '', \realpath($dir)))->eol();
						$deps = \scandir($dir);

						foreach ($deps as $dep)
						{
							// don't touch dot files
							if ( ! \preg_match('#^\..*$#', $dep))
							{
								$fullpath = \realpath(\realpath($dir).'/'.$dep);
								$this->writer->writef('  removing '.\str_replace(\app\Env::key('sys.path'), '', $fullpath))->eol();
								\app\Filesystem::delete($fullpath);
							}
						}

						$this->writer->eol();
						$this->writer->writef(" Running bower install in ".\str_replace(\app\Env::key('sys.path'), '', \realpath($rootdir)))->eol()->eol();
						\chdir($rootdir);
						\passthru("bower install --no-color");
					}
					else
					{
						$this->writer->writef(" No component.json in $rootdir")->eol();
					}
				}
				else # directory property not set
				{
					$this->writer->writef(" No [directory] specified for [$file]. Ignoring.")->eol();
				}
			}

		}
		else # no command
		{
			$this->writer->writef(' No command specified, see -h for help.')->eol();
		}
	}

} # class
