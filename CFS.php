<?php namespace mjolnir\cfs;

// make sure EXT is defined
if ( ! \defined('EXT'))
{
	\define('EXT', '.php');
}

if ( ! \interface_exists('\mjolnir\cfs\CFSInterface', false))
{
	// include interface
	require 'CFSInterface'.EXT;
}

if ( ! \class_exists('\app\Benchmark', false))
{
	// include default benchmarking
	require 'Benchmark'.EXT;

	\class_alias('\mjolnir\cfs\Benchmark', 'app\Benchmark');
}

/**
 * Cascading File System
 *
 * Class cascading based on:
 * https://github.com/srcspider/Cascading-Class-System/blob/master/CCS-Standard.md
 * Supports namespaces as well as PSR-0 compatible. Designed to work with
 * packages and modules. This class is for PHP5.4. It does not load classes, but
 * symbols (classes, traits, interfaces).
 *
 * Configuration and File cascading is based on Kohana3.
 *
 * @author  Ibidem Team
 * @version 1.0
 */
class CFS implements CFSInterface
{
	/**
	 * System module paths.
	 *
	 * @var array paths
	 */
	protected static $modules = [];

	/**
	 * System namespaces
	 *
	 * @var array namespaces to path association
	 */
	protected static $namespaces = [];

	/**
	 * System paths
	 *
	 * @var array paths
	 */
	protected static $paths = [];

	/**
	 * Currently loaded configuration files.
	 * Or, configuration files loaded from cache.
	 *
	 * @var array
	 */
	protected static $cache_config = [];

	/**
	 * @var \mjolnir\types\Stash
	 */
	protected static $cache = null;

	// ------------------------------------------------------------------------
	// Inspection & Loading

	/**
	 * @param string symbol
	 * @param boolean autoload while checking?
	 * @return boolean symbol exists as class, interface, or trait?
	 */
	static function symbol_exists($symbol, $autoload = false)
	{
		return \class_exists($symbol, $autoload)
			|| \interface_exists($symbol, $autoload)
			|| \trait_exists($symbol, $autoload);
	}

	/**
	 * @var array
	 */
	protected static $cache_load_symbol = [];

	/**
	 * @param string symbol name with namespace
	 * @return bool successfully loaded?
	 */
	static function load_symbol($symbol)
	{
		$benchmark = \app\Benchmark::token(__METHOD__, 'Mjolnir');

		// normalize
		$symbol_name = \ltrim($symbol, '\\');

		if ($ns_pos = \strripos($symbol_name, '\\'))
		{
			$namespace = \substr($symbol_name, 0, $ns_pos);
			$symbol_name = \substr($symbol_name, $ns_pos + 1);
		}
		else # class belongs to global namespace
		{
			\app\Benchmark::stop($benchmark);

			// we don't handle classes of the global namespace
			return false;
		}

		if ($namespace === 'app')
		{
			$target = DIRECTORY_SEPARATOR.
				\str_replace('_', DIRECTORY_SEPARATOR, $symbol_name).EXT;

			// cached?
			if (isset(static::$cache_load_symbol[$symbol]))
			{
				$path = static::$cache_load_symbol[$symbol];
				if ($path === null)
				{
					\app\Benchmark::stop($benchmark);

					// failed to load
					return false;
				}
				else # found path last time
				{
					$ns = static::$modules[$path];

					\app\Benchmark::stop($benchmark);

					if ( ! static::symbol_exists($ns.'\\'.$symbol_name, false))
					{
						// found a matching file
						require $path.$target;
					}

					if ($ns !== 'app')
					{
						// alias to app namespace
						\class_alias($ns.'\\'.$symbol_name, $symbol);
					}

					// success
					return true;
				}
			}
			else # not cached; searching...
			{
				foreach (static::$modules as $path => $ns)
				{
					if (\file_exists($path.$target))
					{
						// cache?
						if (static::$cache)
						{
							static::$cache_load_symbol[$symbol] = $path;
							static::$cache->set
								(
									'\mjolnir\cfs\CFS::load_symbol',
									static::$cache_load_symbol,
									static::$cache_file_duration
								);
						}

						\app\Benchmark::stop($benchmark);

						if ( ! static::symbol_exists($ns.'\\'.$symbol_name, false))
						{
							// found a matching file
							require $path.$target;
						}

						// module's namespace is app?
						if ($ns !== 'app')
						{
							// alias to app namespace
							\class_alias($ns.'\\'.$symbol_name, $symbol);
						}

						// success
						return true;
					}
				}

				// cache?
				if (static::$cache)
				{
					static::$cache_load_symbol[$symbol] = null;
					static::$cache->set
						(
							'\mjolnir\cfs\CFS::load_symbol',
							static::$cache_load_symbol,
							static::$cache_file_duration
						);
				}

				\app\Benchmark::stop($benchmark);

				// didn't find the file
				return false;
			}
		}
		else # non \app namespace
		{
			if (isset(static::$namespaces[$namespace]))
			{
				// Normally this file check wouldn't be required but we want to
				// support bridging for backwards compatiblity, which breaks
				// the normal logic of "unique namespaces".
				$file = static::$namespaces[$namespace].DIRECTORY_SEPARATOR
					. \str_replace('_', DIRECTORY_SEPARATOR, $symbol_name).EXT;

				if (\file_exists($file))
				{
					\app\Benchmark::stop($benchmark);

					require $file;

					// success
					return true;
				}
				else # file not found
				{
					\app\Benchmark::stop($benchmark);

					// pass it to bridge; no other autoloaders will find it FYI
					// because of the namespace properties
					return false;
				}
			}
			else # unknown namespace
			{
				$target = DIRECTORY_SEPARATOR.
					\str_replace('_', DIRECTORY_SEPARATOR, $symbol_name).EXT;

				if (isset(static::$cache_load_symbol[$symbol]))
				{
					$path = static::$cache_load_symbol[$symbol];
					if ($path === null)
					{
						\app\Benchmark::stop($benchmark);

						// failed to load
						return false;
					}
					else # found path last time
					{
						$ns = static::$modules[$path];

						\app\Benchmark::stop($benchmark);

						if ( ! static::symbol_exists($ns.'\\'.$symbol_name, false))
						{
							// found a matching file
							require $path.$target;
						}

						\class_alias($ns.'\\'.$symbol_name, $symbol);

						// success
						return true;
					}
				}
				else # not cached
				{
					// we attempt to detect a parent namespace; a parent
					// namespace is specified by following a valid namespace
					// with the keyword segment "\next", the symbol will then
					// be defined as the first valid class bellow the namespace
					// in the hirarchy

					// recomended syntax: class A extends next\A { }
					// note: that's "next\A" not "\next\A"; the current
					// namespace in the context will be autofilled in by PHP

					if (\stripos($namespace, '\next') + 5 == \strlen($namespace))
					{
						$pivot_ns = \substr($namespace, 0, \strlen($namespace) - 5);
						$ns_keys = \array_keys(static::$namespaces);
						$offset = \array_search($pivot_ns, \array_keys(static::$namespaces));

						if ($offset !== false)
						{
							for ($idx = $offset + 1; $idx < \count($ns_keys); ++$idx)
							{
								if (\file_exists(static::$namespaces[$ns_keys[$idx]].$target))
								{
									// cache?
									if (static::$cache)
									{
										static::$cache_load_symbol[$symbol] = static::$namespaces[$ns_keys[$idx]];
										static::$cache->set
											(
												'\mjolnir\cfs\CFS::load_symbol',
												static::$cache_load_symbol,
												static::$cache_file_duration
											);
									}

									\app\Benchmark::stop($benchmark);

									if ( ! static::symbol_exists($ns_keys[$idx].'\\'.$symbol_name, false))
									{
										// found a matching file
										require static::$namespaces[$ns_keys[$idx]].$target;
									}

									\class_alias($ns_keys[$idx].'\\'.$symbol_name, $symbol);

									// success
									return true;
								}
							}
						}
					}
					else # non-parent namespace
					{
						// attempt to locate in subnamespaces
						$parent_ns = $namespace.'\\';
						foreach (static::$modules as $path => $ns)
						{
							if (\strripos($ns, $parent_ns) === 0 && \file_exists($path.$target))
							{
								// cache?
								if (static::$cache)
								{
									static::$cache_load_symbol[$symbol] = $path;
									static::$cache->set
										(
											'\mjolnir\cfs\CFS::load_symbol',
											static::$cache_load_symbol,
											static::$cache_file_duration
										);
								}

								\app\Benchmark::stop($benchmark);

								if ( ! static::symbol_exists($ns.'\\'.$symbol_name, false))
								{
									// found a matching file
									require $path.$target;
								}

								\class_alias($ns.'\\'.$symbol_name, $symbol);

								// success
								return true;
							}
						}
					}
				}

				\app\Benchmark::stop($benchmark);

				// failed to find class
				return false;
			}
		}
	}

	/**
	 * Given a regex pattern, the function will return all classes within the
	 * system who's name (excluding namespace) matches the pattern. The returned
	 * associative array contains the class name (with no namespace) followed by
	 * the namespace for it. Only the top version of all classes is returned.
	 *
	 * @return array classes
	 */
	static function classmatches($pattern)
	{
		$classmatches = [];

		foreach (static::$modules as $path => $namespace)
		{
			$realpath = \realpath($path);

			$raw_class_files = static::find_files('#'.\str_replace('.', '\\.', EXT).'#', [$realpath]);

			// filter out non-class files; and cleanup class files
			foreach ($raw_class_files as $file)
			{
				$file = \ltrim(\str_replace($realpath, '', \realpath($file)), '\\/');
				if (\strpos($file, static::APPDIR) !== 0)
				{
					// convert file to class
					$file = \substr($file, 0, \strlen($file) - \strlen(EXT));
					$class = \preg_replace('#[/\\\\]#', '_', $file);

					if (\preg_match($pattern, $class))
					{
						if ( ! isset($classmatches[$class]))
						{
							$classmatches[$class] = $namespace;
						}
					}
				}
			}
		}

		return $classmatches;
	}

	// ------------------------------------------------------------------------
	// Configuration

	/**
	 * Defines modules with which the autoloaded will work with. Modules are an
	 * array of paths pointing to namespaces. Each namespace must be unique,
	 * except when using the namespace "app" which may be mapped to any number
	 * of paths.
	 *
	 * @param array modules
	 */
	static function modules(array $modules)
	{
		static::$modules = $modules;

		// namespace mapping
		static::$namespaces = \array_flip($modules);
		if (isset(static::$namespaces['app']))
		{
			// we consider the app value special, so it's invalid for our
			// namespace mapping
			unset(static::$namespaces['app']);
		}

		// compute paths;
		$paths = \array_keys($modules);
		static::$paths = [];
		foreach ($paths as $path)
		{
			static::$paths[] = \rtrim($path, DIRECTORY_SEPARATOR).
				DIRECTORY_SEPARATOR.static::APPDIR.DIRECTORY_SEPARATOR;
		}
	}

	/**
	 * Prepend extra modules. For use in conditional module includes such as the
	 * case of development-only modules.
	 *
	 * @param array modules
	 */
	static function frontmodules(array $modules)
	{
		static::$modules = \array_reverse(static::$modules, true);
		static::$paths = \array_reverse(static::$paths);
		foreach (\array_reverse($modules, true) as $path => $namespace)
		{
			static::$modules[$path] = $namespace;
			static::$paths[] = \rtrim($path, DIRECTORY_SEPARATOR).
				DIRECTORY_SEPARATOR.static::APPDIR.DIRECTORY_SEPARATOR;
		}

		static::$modules = \array_reverse(static::$modules, true);
		static::$paths = \array_reverse(static::$paths);
	}

	/**
	 * Append extra modules. For use in conditional module includes such as the
	 * case of development-only modules.
	 *
	 * @param array modules
	 */
	static function backmodules(array $modules)
	{
		foreach ($modules as $path => $namespace)
		{
			static::$modules[$path] = $namespace;
			static::$paths[] = \rtrim($path, DIRECTORY_SEPARATOR).
				DIRECTORY_SEPARATOR.static::APPDIR.DIRECTORY_SEPARATOR;
		}
	}

	/**
	 * Specifies some special namespaces that are not suppose to map as modules.
	 * A very simple example of this are interface modules. Interfaces are
	 * suppose to be unique; you're not suppose to overwrite them. So it makes
	 * no sense to search for them as modules; wasted checks.
	 *
	 * @param array namespace paths
	 */
	static function namespacepaths(array $namespace_paths)
	{
		foreach ($namespace_paths as $namespace => $path)
		{
			static::$namespaces[$namespace] = $path;
		}
	}

	/**
	 * Appends extra paths to front of current paths.
	 *
	 * @param array paths
	 */
	static function frontpaths(array $paths)
	{
		$new_paths = $paths;
		foreach (static::$paths as $path)
		{
			$new_paths[] = $path;
		}
		static::$paths = $new_paths;
	}

	/**
	 * Appends extra paths to back of current paths.
	 *
	 * @param array paths
	 */
	static function backpaths(array $paths)
	{
		foreach ($paths as $path)
		{
			static::$paths[] = $path;
		}
	}

	// ------------------------------------------------------------------------
	// Paths Retrieval

	/**
	 * @var array
	 */
	protected static $cache_file = [];

	/**
	 * Returns the first file in the file system that matches. Or null.
	 *
	 * @param string relative file path
	 * @param string file extention
	 * @return string path to file; or null
	 */
	static function file($file, $ext = EXT)
	{
		$file .= $ext;
		// check if we didn't get asked for it already
		if (isset(static::$cache_file[$file]))
		{
			return static::$cache_file[$file];
		}
		else # no file cache entry
		{
			// find file
			foreach (static::$paths as $path)
			{
				if (\file_exists($path.$file))
				{
					static::$cache_file[$file] = \realpath($path.$file);

					// success
					return \realpath($path.$file);
				}
			}
		}

		// failed
		return null;
	}

	/**
	 * @var array
	 */
	protected static $cache_file_list = [];

	/**
	 * @param string relative file path
	 * @param string file extention
	 * @return array files (or empty array)
	 */
	static function file_list($file, $ext = EXT)
	{
		// append extention
		$file = $file.$ext;
		// find files
		if (isset(static::$cache_file_list[$file]))
		{
			return static::$cache_file_list[$file];
		}
		else # no cache entry
		{
			$files = [];
			foreach (static::$paths as $path)
			{
				if (\file_exists($path.$file))
				{
					$files[] = $path.$file;
				}
			}

			// cache?
			if (static::$cache)
			{
				static::$cache_file_list[$file] = $files;
				static::$cache->set
					(
						'\mjolnir\cfs\CFS::file_list',
						static::$cache_file_list,
						static::$cache_file_duration
					);
			}

			return $files;
		}
	}

	/**
	 * Find all files matching the pattern.
	 *
	 * If context is provided uses that as base for searching, if context is not
	 * provided the function will search all module files (which is to say no
	 * class files will be searched) for the given pattern.
	 *
	 * This function is recursive and both returns an array of matches as well
	 * as populate a matches variable if provided (internally it will use a
	 * matches variable anyway).
	 *
	 * @return array matched files
	 */
	static function find_files($pattern, array $contexts = null, array &$matches = [])
	{
		if ($contexts === null)
		{
			$contexts = static::paths();
		}

		foreach ($contexts as $path)
		{
			$subdirs = [];

			// clenaup path
			$path = \rtrim($path, '\\/').'/';

			$dir = \dir($path);
			while (false !== ($entry = $dir->read()))
			{
				$entrypath = $path.$entry;
				if ($entry != '.' && $entry != '..' && \is_dir($entrypath))
				{
					$subdirs[] = $entrypath;
				}
				else if (\is_file($entrypath) && \preg_match($pattern, $entry))
				{
					$matches[] = $entrypath;
				}
			}
			$dir->close();

			static::find_files($pattern, $subdirs, $matches);
		}

		return $matches;
	}

	/**
	 * @param string namespace
	 * @return string path
	 */
	static function modulepath($namespace)
	{
		return static::$namespaces[\ltrim($namespace, '\\')].DIRECTORY_SEPARATOR;
	}

	/**
	 * @param string namespace
	 * @return string class path
	 */
	static function classpath($namespace)
	{
		return static::modulepath($namespace);
	}

	/**
	 * @param string namespace
	 * @return string file path
	 */
	static function filepath($namespace)
	{
		return static::modulepath($namespace)
			. \mjolnir\cfs\CFSInterface::APPDIR
			. DIRECTORY_SEPARATOR;
	}

	/**
	 * Returns the first directory in the file system that matches. Or false.
	 *
	 * [!!] use this method only when you need paths to resources that require
	 * static file relationships; ie. sass scripts style folder, coffee script
	 * folders, etc.
	 *
	 * @param string relative dir path
	 * @return string path to dir; or null
	 */
	static function dir($dir_path)
	{
		$benchmark = \app\Benchmark::token(__METHOD__, 'Mjolnir');

		// check if we didn't get asked for it last time; or if it's cached
		if (isset(static::$cache_file[$dir_path]))
		{
			\app\Benchmark::stop($benchmark);

			return static::$cache_file[$dir_path];
		}
		else # no file cache entry
		{
			// find file
			foreach (static::$paths as $path)
			{
				if (\file_exists($path.$dir_path))
				{
					$path = \realpath($path.$dir_path).DIRECTORY_SEPARATOR;
					static::$cache_file[$dir_path] = $path;
					// cache?
					if (static::$cache)
					{
						static::$cache->set
							(
								'\mjolnir\cfs\CFS::file',
								static::$cache_file,
								static::$cache_file_duration
							);
					}

					\app\Benchmark::stop($benchmark);

					// success
					return $path;
				}
			}
		}

		\app\Benchmark::stop($benchmark);

		// failed
		return null;
	}

	// ------------------------------------------------------------------------
	// Configuration Files

	/**
	 * @param string configuration key (any valid file syntax)
	 * @return array configuration or empty array
	 */
	static function config($key, $ext = EXT)
	{
		return static::configfile($key, $ext);
	}

	/**
	 * Loads a configuration based on key given. All configuration files
	 * matching the key are merged down and the resulting array is returned.
	 *
	 * In the case of numeric key arrays, ie. array(1, 2, 3), the unique values,
	 * as determined by \in_array will be appended. [!!] This means that key
	 * order is guranteed but numeric keys will be in proper order if the
	 * bottom configuration file itself didn't have explicit numeric keys.
	 *
	 * The function does not gurantee the configuration keys and values in the
	 * case of numeric key arrays will be in any specific order in the final
	 * output. If you wish to store the order of keys then it is recomended you
	 * store a sort order hint and apply a sorting function on retrieval.
	 *
	 * This function does not support dynamicly altered configuration files
	 * during the application execution. It will not process a key it has
	 * already loaded once, but instead return the previously computed
	 * configuration.
	 *
	 * @param string configuration key (any valid file syntax)
	 * @return array configuration or empty array
	 */
	static function configfile($key, $ext = EXT)
	{
		$benchmark = \app\Benchmark::token(__METHOD__, 'Mjolnir');

		// check if we didn't get asked for it last time; or if it's cached
		// this is NOT a persistent cache, only a temporary one
		if (isset(static::$cache_config[$key.$ext]))
		{
			\app\Benchmark::stop($benchmark);

			return static::$cache_config[$key.$ext];
		}
		else # not cached
		{
			// we start at the bottom since we merge up
			$files = \array_reverse
				(
					static::file_list
						(static::CNFDIR.DIRECTORY_SEPARATOR.$key, $ext)
				);

			// merge everything
			$key .= $ext;
			static::$cache_config[$key] = [];

			foreach ($files as $file)
			{
				$file_contents = include $file;

				if (\is_array($file_contents))
				{
					static::config_merge
						(static::$cache_config[$key], $file_contents);
				}
				else # not array
				{
					\app\Benchmark::stop($benchmark);

					$corrupt_file = \str_replace(DOCROOT, '', $file);
					throw new \app\Exception
						('Corrupt configuration file ['.$corrupt_file.']');
				}
			}

			\app\Benchmark::stop($benchmark);

			// if there were no files this will be empty; which is fine
			return static::$cache_config[$key];
		}
	}

	// ------------------------------------------------------------------------
	// System Information

	/**
	 * Current module declarations.
	 *
	 * @return array
	 */
	static function system_modules()
	{
		return static::$modules;
	}

	/**
	 * @return array all known paths
	 */
	static function paths()
	{
		return static::$paths;
	}

	/**
	 * @return array namespace to path map
	 */
	static function namespaces()
	{
		return static::$namespaces;
	}

	// ------------------------------------------------------------------------
	// General Helpers

	/**
	 * Merge configuration arrays.
	 *
	 * This function does not return a new array, the first array is simply
	 * processed directly; for effeciency.
	 *
	 * Behaviour: numeric key arrays are appended to one another, any other key
	 * and the values will overwrite.
	 *
	 * @param array base
	 * @param array overwrite
	 */
	static function config_merge(array &$base, array &$overwrite)
	{
		foreach ($overwrite as $key => &$value)
		{
			if (\is_int($key))
			{
				// add only if it doesn't exist
				if ( ! \in_array($overwrite[$key], $base))
				{
					$base[] = $overwrite[$key];
				}
			}
			else if (\is_array($value))
			{
				if (isset($base[$key]) && \is_array($base[$key]))
				{
					static::config_merge($base[$key], $value);
				}
				else # does not exist or it's a non-array
				{
					$base[$key] = $value;
				}
			}
			else # not an array and not numeric key
			{
				$base[$key] = $value;
			}
		}
	}

	/**
	 * Applies config_merge, but returns array and doesn't alter base.
	 *
	 * @param array base
	 * @param array overwrite
	 * @return array merged configuration
	 */
	static function merge(array $base, array &$overwrite)
	{
		static::config_merge($base, $overwrite);
		return $base;
	}

	// ------------------------------------------------------------------------
	// Utility

	/**
	 * @var int
	 */
	protected static $cache_file_duration = null;

	/**
	 * Cache object is used on symbol, configuration and file system caching. Or
	 * at least that's the intention.
	 */
	static function cache (
			\mjolnir\types\Stash $cache = null,
			$file_duration = 1800 /* 30 minutes */
		)
	{
		// got cache? or reset?
		if ($cache)
		{
			static::$cache_file = $cache->get
				('\mjolnir\cfs\CFS::file', []);

			static::$cache_file_list = $cache->get
				('\mjolnir\cfs\CFS::file_list', []);

			static::$cache_load_symbol = $cache->get
				('\mjolnir\cfs\CFS::load_symbol', []);

			static::$cache_file_duration = $file_duration;

			static::$cache = $cache;
		}
		else # reset cache
		{
			static::$cache_file = [];
			static::$cache_file_list = [];
			static::$cache_load_symbol = [];
			static::$cache_file_duration = null;

			static::$cache = null;
		}
	}

} # class
