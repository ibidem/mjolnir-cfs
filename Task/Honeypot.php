<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Honeypot extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$ns = $this->get('namespace', '');
		$verbose = $this->get('verbose', false);

		if (empty($ns))
		{
			foreach (\app\CFS::system_modules() as $path => $namespace)
			{
				\app\Task::invoke('honeypot')
					->set('namespace', $namespace)
					->set('verbose', $verbose)
					->writer_is($this->writer)
					->run();
			}

			return;
		}

		$modules = \array_flip(\app\CFS::system_modules());
		if (isset($modules[$ns]))
		{
			$written = false;
			$path = $modules[$ns];
			$files = static::files($path);
			$output = '<?php namespace app;';
			$output .= PHP_EOL.PHP_EOL.'// This is an IDE honeypot. It tells IDEs the class hirarchy, but otherwise has'
					. PHP_EOL.'// no effect on your application. :)';

			$output .= PHP_EOL.PHP_EOL.'// HowTo: '.\app\Overlord::commandname().' honeypot -n \''.$ns.'\''.PHP_EOL.PHP_EOL;

			foreach ($files as $file)
			{
				if (\preg_match('#\+#', $file))
				{
					continue;
				}

				if ($verbose)
				{
					if ($written)
					{
						$this->writer->printf('reset');
					}

					$this->writer->writef(' Reading: '.$file);
					$written = true;
				}

				if (\preg_match('#^Trait_#', $file))
				{
					$output .= 'trait '.$file.' { use \\'.$ns.'\\'.$file.'; }'.PHP_EOL;
				}
				else if ( ! \preg_match('#^Enum_#', $file) && ! \preg_match('#\\\types#', $ns))
				{
					$output .= $this->reflection_strategy($ns, $file);
				}
			}

			$dir = $path.DIRECTORY_SEPARATOR.\app\CFS::APPDIR.DIRECTORY_SEPARATOR;

			if ( ! \is_dir($dir))
			{
				\mkdir($dir, 0777, true);
			}

			\file_put_contents($dir.'honeypot'.EXT, $output);

			if ($written)
			{
				$this->writer->printf('reset');
			}

			$this->writer->writef(' Succesfully created '.\str_replace('\\', '/', \str_replace(DOCROOT, '', $dir.'honeypot'.EXT)))->eol();
		}
		else # namespace does not exist
		{
			throw new \app\Exception
				('No such namespace registered; please check your [environment.php] file.');
		}
	}

	/**
	 * Computes hinting tacking into consideration doc comments; this is very
	 * powerful since it enables autocomplete functionality for fluent
	 * interfaces.
	 *
	 * @return string
	 */
	protected function reflection_strategy($ns, $class)
	{
		// throw new \app\Exception_NotImplemented('Reflectin strategy is not currently available.');

		$current_class = "\\$ns\\$class";
		$app_class = "\\app\\$class";
		$reflection_class = new \ReflectionClass("\\$ns\\$class");
		$methods = $reflection_class->getMethods();

		$fluency = '';
		$influence = '';
		foreach ($methods as $method)
		{
			if (\strpos($method->getDocComment(), '@return static') !== false)
			{
				$params = \app\Arr::implode
					(
						', ',
						$method->getParameters(),
						function ($key, $param)
						{
							$param_str = '';

							if ($param->isArray())
							{
								$param_str .= 'array ';
							}

							if ($param->isPassedByReference())
							{
								$param_str .= ' & ';
							}

							$param_str .= '$'.$param->getName();

							if ($param->isDefaultValueAvailable())
							{
								$default = $param->getDefaultValue();
								if (\is_null($default))
								{
									$param_str .= ' = null';
								}
								else # not null
								{
									$param_str .= ' = '.\var_export($default, true);
								}
							}

							return $param_str;
						}
					);

				$naked_params = \app\Arr::implode
					(
						', ',
						$method->getParameters(),
						function ($key, $param)
						{
							return '$'.$param->getName();
						}
					);


				if ( ! $method->isStatic())
				{
					$fluency .= " * @method $app_class {$method->name}($params)\n";
				}
				else # static method
				{
					$influence .= "/** @return $app_class */ static function {$method->name}($params) { return parent::{$method->name}($naked_params); }";
				}
			}
		}

		if ($fluency !== '')
		{
			$fluency = "/**\n$fluency */\n";
		}

		return "\n{$fluency}class $class extends $current_class { $influence }\n";
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