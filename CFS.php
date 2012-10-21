<?php namespace mjolnir\cfs;

// make sure EXT is defined
if ( ! \defined('EXT'))
{
	\define('EXT', '.php');
}

if ( ! \interface_exists('\mjolnir\cfs\CFSCompatible', false))
{
	require 'CFSCompatible.php';
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
class CFS implements \mjolnir\cfs\CFSCompatible
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
	 * @var \mjolnir\types\Storage
	 */
	protected static $storage = null;

	/**
	 * @var \mjolnir\types\Cache
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
		return
			\class_exists($symbol, $autoload) ||
			\interface_exists($symbol, $autoload) ||
			\trait_exists($symbol, $autoload);
	}

	/**
	 * @var array
	 */
	private static $cache_load_symbol = [];

	/**
	 * @param string symbol name with namespace
	 * @return bool successfully loaded?
	 */
	static function load_symbol($symbol)
	{
		// normalize
		$symbol_name = \ltrim($symbol, '\\');

		if ($ns_pos = \strripos($symbol_name, '\\'))
		{
			$namespace = \substr($symbol_name, 0, $ns_pos);
			$symbol_name = \substr($symbol_name, $ns_pos + 1);
		}
		else # class belongs to global namespace
		{
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
					// failed to load
					return false;
				}
				else # found path last time
				{
					$ns = static::$modules[$path];
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

						// cache?
						if (static::$cache)
						{
							$cache_load_symbol[$symbol] = $path;
							static::$cache->store
								(
									'\mjolnir\cfs\CFS::load_symbol',
									static::$cache_load_symbol,
									static::$cache_file_duration
								);
						}

						// success
						return true;
					}
				}

				// cache?
				if (static::$cache)
				{
					$cache_load_symbol[$symbol] = null;
					static::$cache->store
						(
							'\mjolnir\cfs\CFS::load_symbol',
							static::$cache_load_symbol,
							static::$cache_file_duration
						);
				}

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
					require $file;

					// success
					return true;
				}
				else # file not found
				{
					// pass it to bridge; no other autoloaders will find it FYI
					// because of the namespace properties
					return false;
				}
			}
			else # unknown namespace
			{
				// failed to find class
				return false;
			}
		}
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
		static::$paths = array();
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
	private static $cache_file = [];

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
		// check if we didn't get asked for it last time; or if it's cached
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
					// cache?
					if (static::$cache)
					{
						static::$cache->store
							(
								'\mjolnir\cfs\CFS::file',
								static::$cache_file,
								static::$cache_file_duration
							);
					}
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
	private static $cache_file_list = [];

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
			$files = array();
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
				static::$cache->store
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
			. \mjolnir\cfs\CFSCompatible::APPDIR
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
		// check if we didn't get asked for it last time; or if it's cached
		if (isset(static::$cache_file[$dir_path]))
		{
			return static::$cache_file[$dir_path];
		}
		else # no file cache entry
		{
			// find file
			foreach (static::$paths as $path)
			{
				if (\file_exists($path.$dir_path))
				{
					static::$cache_file[$dir_path] = $path.$dir_path;
					// cache?
					if (static::$cache)
					{
						static::$cache->store
							(
								'\mjolnir\cfs\CFS::file',
								static::$cache_file,
								static::$cache_file_duration
							);
					}
					// success
					return \realpath($path.$dir_path).DIRECTORY_SEPARATOR;
				}
			}
		}

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
		return static::config_file($key, $ext);
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
	static function config_file($key, $ext = EXT)
	{
		// check if we didn't get asked for it last time; or if it's cached
		if (isset(static::$cache_config[$key.$ext]))
		{
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
			static::$cache_config[$key] = array();

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
					$corrupt_file = \str_replace(DOCROOT, '', $file);
					echo 'configuration file ['.$corrupt_file.'] is corrupt';
					throw new \app\Exception
						('Corrupt configuration file ['.$corrupt_file.']');
				}
			}

			// storage support?
			if (static::$storage)
			{
				$serialized_config = \array_pop
					(
						static::$storage->fetch
							(array(static::$storage_config_key => $key))
					);
				static::config_merge
					(
						static::$cache_config[$key],
						\unserialize
							($serialized_config[static::$storage_value_key])
					);
			}
			// cache?
			if (static::$cache)
			{
				static::$cache->store
					(
						'\mjolnir\cfs\CFS::config',    # key
						static::$cache_config,         # value
						static::$cache_config_duration # duration
					);
			}

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
	static function config_merge(array & $base, array & $overwrite)
	{
		foreach ($overwrite as $key => & $value)
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
	static function merge(array $base, array & $overwrite)
	{
		static::config_merge($base, $overwrite);
		return $base;
	}

	// ------------------------------------------------------------------------
	// Utility

	/**
	 * @var string
	 */
	private static $storage_config_key;

	/**
	 * @var string
	 */
	private static $storage_value_key;

	/**
	 * Sets local persistent storage object to use when retrieving
	 * configurations files. The object should be preconfigured.
	 *
	 * @param \mjolnir\types\Storage
	 * @param string key that identifies configuration name (no EXT)
	 * @param string key that identifies serialized object
	 */
	static function storage	(
			\mjolnir\types\Storage $storage = null,
			$config_key = 'config',
			$value_key = 'serialized'
		)
	{
		static::$storage = $storage;
		// got storage? or reset?
		if ($storage)
		{
			static::$storage_config_key = $config_key;
			static::$storage_value_key = $value_key;
		}
	}

	/**
	 * @var int
	 */
	private static $cache_file_duration;

	/**
	 * @var int
	 */
	private static $cache_config_duration;

	/**
	 * Cache object is used on symbol, configuration and file system caching. Or
	 * at least that's the intention.
	 *
	 * @param \mjolnir\types\Cache
	 * @param int duration for files
	 * @param int duration for configs
	 */
	static function cache (
			\mjolnir\types\Cache $cache = null,
			$file_duration = 1800 /* 30 minutes */,
			$config_duration = 300 /* 5 minutes */
		)
	{
		static::$cache = $cache;
		// got cache? or reset?
		if ($cache)
		{
			static::$cache_config = $cache->fetch
				('\mjolnir\cfs\CFS::config', array());

			static::$cache_file = $cache->fetch
				('\mjolnir\cfs\CFS::file', array());

			static::$cache_file_list = $cache->fetch
				('\mjolnir\cfs\CFS::file_list', array());

			static::$cache_load_symbol = $cache->fetch
				('\mjolnir\cfs\CFS::load_symbol', array());

			static::$cache_file_duration = $file_duration;
			static::$cache_config_duration = $config_duration;
		}
	}

} # class
