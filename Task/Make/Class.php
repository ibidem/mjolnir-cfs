<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Make_Class extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * @var string
	 */
	protected static $filetype = 'class';

	/**
	 * @return string
	 */
	protected function class_file($class_name, $namespace, $category, $library, array $config)
	{
		$file = "<?php namespace $namespace;".PHP_EOL.PHP_EOL;

		if (isset($config['disclaimer']) && $config['disclaimer'])
		{
			$file .= '/* '.\wordwrap($config['disclaimer'], 77, PHP_EOL.' * ')
				. PHP_EOL.' */'.PHP_EOL.PHP_EOL;
		}

		// base namespace is package
		$package = \preg_replace('#\\\\.*$#', '', $namespace);

		$file .= '/**'.PHP_EOL
			. ' * @package    '.$package.PHP_EOL
			. ' * @category   '.$category.PHP_EOL
			. ' * @author     '.$config['author'].PHP_EOL
			. ' * @copyright  (c) '.\date('Y').', '.$config['author'].' Team'.PHP_EOL
			;

		if (isset($config['license']) && $config['license'])
		{
			$file .= ' * @license    '.$config['license'].PHP_EOL;
		}

		$file .= ' */'.PHP_EOL;

		$conventions_config = \app\CFS::config('mjolnir/conventions');

		if (\preg_match('#^Trait_.*$#', $class_name))
		{
			static::$filetype = 'trait';
			$library = true;
		}

		if ($library)
		{
			$file .= static::$filetype." $class_name".PHP_EOL;
		}
		else # not library
		{
			$extention = '\\app\\Instantiatable';
			foreach ($conventions_config['base_classes'] as $regex => $class_extention)
			{
				if (\preg_match($regex, $class_name))
				{
					$extention = $class_extention;
				}
			}

			$file .= "class $class_name extends $extention".PHP_EOL;
		}

		$file .= '{'.PHP_EOL;

		foreach ($conventions_config['autofills'] as $regex => $fill)
		{
			if (\preg_match($regex, $class_name))
			{
				$file .= $fill.PHP_EOL;
			}
		}

		$file .=
			// the extra . is to avoid IDE's picking this code up unintentionally
			  "\t// @"."todo write implementation for \\{$namespace}\\{$class_name}".PHP_EOL
			. PHP_EOL
			. '} # '.static::$filetype.PHP_EOL
			;

		return $file;
	}

	/**
	 * @param string class name
	 * @param string namespace
	 * @return string
	 */
	protected function test_file($class_name, $namespace, $category, array $config)
	{
		// base namespace is package
		$package = \preg_replace('#\\\\.*$#', '', $namespace);

		$file
			= "<?php namespace $namespace;".PHP_EOL
			. PHP_EOL
			. '/**'.PHP_EOL
			. ' * @package    '.$package.PHP_EOL
			. ' * @category   '.$category.PHP_EOL
			. ' * @author     '.$config['author'].PHP_EOL
			. ' * @copyright  (c) '.\date('Y').', '.$config['author'].PHP_EOL
			;

		if (isset($config['license']) && $config['license'])
		{
			$file .= ' * @license    '.$config['license'].PHP_EOL;
		}

		$file .= " * @see \\{$namespace}\\{$class_name}".PHP_EOL;
		$file .= ' */'.PHP_EOL;

		$file .=
			  "class {$class_name}Test extends \\PHPUnit_Framework_TestCase".PHP_EOL
			. '{'.PHP_EOL
			// the extra . is to avoid IDE's picking them up
			. "\t// @"."todo write tests for \\{$namespace}\\{$class_name}".PHP_EOL
			. PHP_EOL
			. '} # class'.PHP_EOL
			;

		return $file;
	}

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$category = $this->get('category');
		$category = $category ? $category : null;

		$class = $this->get('class', false);
		$with_tests = $this->get('with-tests', false);
		$library = $this->get('library', false);
		$forced = $this->get('forced', false);

		// normalize class
		$class = \ltrim($class, '\\');
		$ns_div = \strrpos($class, '\\');
		// does class have namespace?
		if ($ns_div === false)
		{
			$this->writer
				->printf('error', 'You must provide fully qualified '.static::$filetype.' name.')->eol();
			return;
		}

		$namespace = \substr($class, 0, $ns_div);
		$class_name = \substr($class, $ns_div + 1);

		if ($namespace === 'app')
		{
			$namespace = \app\CFS::config('mjolnir/conventions')['app.namespace'];

			if ($namespace === null)
			{
				throw new \app\Exception
					('You need to define what is considered the default namespace in your convention file (key is [app.namespace]).');
			}
		}

		if ($category === null)
		{
			if ($library)
			{
				$category = 'Library';
			}
			else # non-library
			{
				if (($class_div = \strpos($class_name, '_')) !== false)
				{
					// use class name as category
					$category = \substr($class_name, 0, $class_div);
				}
				else # class doesn't have underscore
				{
					// use last segment of namespace as category
					if (($namespace_div = \strrpos($namespace, '\\')) !== false)
					{
						$category = \ucfirst(\substr($namespace, $namespace_div + 1));
					}
					else # did not find '\\'
					{
						// use entire namespace; these are special cases--you
						// typically (should) have two segments
						$category = \ucfirst($namespace);
					}
				}
			}
		}

		$modules = \app\CFS::system_modules();
		$namespaces = \array_flip($modules);

		// module path exists?
		if ( ! isset($namespaces[$namespace]) || ! \file_exists($namespaces[$namespace]))
		{
			$this->writer
				->printf('error', 'Module ['.$namespace.'] doesn\'t exist; you can use make:module to create it')->eol();
			return;
		}

		$module_path = $namespaces[$namespace];

		// load project configuration
		$project_file = $module_path.DIRECTORY_SEPARATOR
			. \mjolnir\cfs\CFSCompatible::APPDIR.DIRECTORY_SEPARATOR
			. \mjolnir\cfs\CFSCompatible::CNFDIR.DIRECTORY_SEPARATOR
			. 'mjolnir'.DIRECTORY_SEPARATOR.'project'.EXT
			;

		// module specific project file?
		if (\file_exists($project_file))
		{
			$config = include $project_file;
		}
		else # no project file; use global
		{
			$config = \app\CFS::config('mjolnir/project');
		}

		// does project file exist?
		if (empty($config) || ! isset($config['author']))
		{
			$this->writer
				->printf('error', 'The [ibidem/project] configuration is empty or missing'
					. ' required paramters.')
				->eol();

			$this->writer
				->printf('status', 'Help', 'This module requires author. Optionally, you '
					. 'can include also disclaimer and license.')
				->eol();

			return;
		}

		$class_div = \strrpos($class_name, '_');
		$class_path = '';
		$class_file = '';
		// has underscore?
		if ($class_div === false)
		{
			$class_path = DIRECTORY_SEPARATOR;
			$class_file = $class_name.EXT;
			$test_class_file = $class_name.'Test'.EXT;
		}
		else # found div
		{
			$class_path = DIRECTORY_SEPARATOR
				. \str_replace
					(
						'_', # search
						DIRECTORY_SEPARATOR, # replace
						\substr($class_name, 0, $class_div)
					)
				. DIRECTORY_SEPARATOR;

			$class_file = \substr($class_name, $class_div + 1).EXT;
			$test_class_file = \substr($class_name, $class_div + 1).'Test'.EXT;
		}
		// file exists?
		if ( ! $forced && \file_exists($module_path.$class_path.$class_file))
		{
			$this->writer
				->printf('error', \ucfirst(static::$filetype).' exists. Use --forced if you want to overwrite.')
				->eol();

			return;
		}

		// create path
		$full_path = $module_path.$class_path;
		\file_exists($full_path) or \mkdir($full_path, 0777, true);

		// create class
		\file_put_contents
			(
				$full_path.$class_file,
				static::class_file($class_name, $namespace, $category, $library, $config)
			);

		// notify
		$this->writer->printf('status', 'Info', 'Class created.')->eol();

		// create tests?
		if ($with_tests)
		{
			// create test
			$test_path = $module_path.DIRECTORY_SEPARATOR
				. \mjolnir\cfs\CFSCompatible::APPDIR.DIRECTORY_SEPARATOR.'tests'
				. DIRECTORY_SEPARATOR.\ltrim($class_path, '\\');

			\file_exists($test_path) or \mkdir($test_path, 0777, true);

			\file_put_contents
				(
					$test_path.$test_class_file,
					static::test_file($class_name, $namespace, $category, $config)
				);

			// notify
			$this->writer->status('Info', 'Test class created.')->eol();
		}

		// update honeypot
		$this->writer->printf('status', 'Info', 'Updating honeypot...')->eol();

		\app\Task::invoke('honeypot')
			->set('namespace', $namespace)
			->writer_is($this->writer)
			->run();
	}

} # class