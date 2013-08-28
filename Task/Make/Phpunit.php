<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Task
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Make_Phpunit extends \app\Task_Base
{
	/**
	 * Shorthand command.
	 */
	function run()
	{
		$ns = \trim($this->get('namespace', ''), '\\');
		$prefix = $this->get('prefix', '');
		$ds = DIRECTORY_SEPARATOR;

		$classtemplatefile = \app\CFS::file('phpunit.class', '.mjtpl');
		if ($classtemplatefile == null)
		{
			throw new \app\Exception('Missing class template. Aborting.');
		}
		else # no errors
		{
			$classtemplate = \file_get_contents($classtemplatefile);
		}

		$traittemplatefile = \app\CFS::file('phpunit.trait', '.mjtpl');

		if ($traittemplatefile == null)
		{
			throw new \app\Exception('Missing trait template. Aborting.');
		}
		else # no errors
		{
			$traittemplate = \file_get_contents($traittemplatefile);
		}

		if (empty($ns))
		{
			foreach (\app\CFS::system_modules() as $path => $namespace)
			{
				if (empty($prefix) || \preg_match('#^'.$prefix.'#', $namespace))
				{
					\app\Task::invoke('make:phpunit')
						->set('namespace', $namespace)
						->writer_is($this->writer)
						->run();
				}
			}

			return;
		}

		$modules = \array_flip(\app\CFS::system_modules());
		if (isset($modules[$ns]))
		{
			$path = $modules[$ns];
			$files = static::files($path);

			$testpath = $path.$ds.\app\CFS::APPDIR.$ds.'tests'.$ds;

			foreach ($files as $classname)
			{
				if (\preg_match('#\+#', $classname))
				{
					continue;
				}

				$testfile = $testpath.\str_replace('_', '/', $classname).'Test'.EXT;

				if (\file_exists($testfile))
				{
					continue;
				}

				if (\interface_exists($ns.'\\'.$classname))
				{
					continue;
				}

				$this->writer
					->writef(' Generated '.\str_replace(\str_replace('\\', '/', \app\Env::key('sys.path')), '', \str_replace('\\', '/', $testfile)))
					->eol();

				if (\preg_match('#^Trait_#', $classname))
				{
					$output = $traittemplate;
					$output = \str_replace('placeholder\ns', $ns, $output);
					$output = \str_replace('Trait_Placeholder', $classname, $output);
				}
				else if ( ! \preg_match('#^Enum_#', $classname) && ! \preg_match('#\\\types#', $ns))
				{
					$output = $classtemplate;
					$output = \str_replace('placeholder\ns', $ns, $output);
					$output = \str_replace('Placeholder_Class', $classname, $output);
				}

				\app\Filesystem::puts($testfile, $output);
			}
		}
		else # namespace does not exist
		{
			throw new \app\Exception
				('No such namespace registered; please check your [environment.php] file.');
		}
	}

	/**
	 * ...
	 */
	protected static function files($path, $prefix = '')
	{
		static $exclude_pattern = array('..', '.', \app\CFS::APPDIR);

		$ext_pattern = '#'.\str_replace('.', '\.', EXT).'$#';
		$files = \array_diff(\scandir($path), $exclude_pattern);
		$clean_files = [];

		foreach ($files as $file)
		{
			if (\is_dir($path.DIRECTORY_SEPARATOR.$file))
			{
				$clean_files = \array_merge
					(
						$clean_files,
						static::files($path.DIRECTORY_SEPARATOR.$file, $prefix.$file.'_')
					);
			}
			else if (\preg_match($ext_pattern, $file))
			{
				// found a class file
				$clean_files[] = $prefix.\preg_replace($ext_pattern, '', $file);
			}
		}

		return $clean_files;
	}

} # class
