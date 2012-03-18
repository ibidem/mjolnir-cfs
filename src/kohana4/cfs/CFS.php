<?php namespace kohana4\cfs;

// make sure EXT is defined
if ( ! \defined('EXT'))
{
	\define('EXT', '.php');
}
	
/**
 * Cascading File System
 * 
 * Class cascading based on: 
 * https://github.com/srcspider/Cascading-Class-System/blob/master/CCS-Standard.md
 * Supports namespaces as well as is PSR-0 compatible. Designed to work with 
 * packages and modules and compatible, but seperate entities.
 * 
 * Configuration and File cascading is based on Kohana3.
 * 
 * @version 1.0
 */
final class CFS
{
	const APPDIR = 'App';
	const CNFDIR = 'config';
	
	/**
	 * System module paths.
	 *
	 * @var array paths
	 */
	private static $modules = array();
	
	/**
	 * System namespaces
	 *
	 * @var array namespaces to path association
	 */
	private static $namespaces = array();
	
	/**
	 * System paths
	 * 
	 * @var array paths 
	 */
	private static $paths = array();
	
	/**
	 * @param string symbol
	 * @param boolean autoload while checking?
	 * @return boolean symbol exists as class, interface, trait?
	 */
	public static function symbol_exists($symbol, $autoload = false)
	{
		return 
			\class_exists($symbol, $autoload) || 
			\interface_exists($symbol, $autoload) ||
			(PHP_VERSION_ID >= 50400 && \trait_exists($symbol, $autoload));
	}
	
	/**
	 * Defines modules with which the autoloaded will work with. Modules are an
	 * array of paths pointing to namespaces. Each namespace must be unique, 
	 * except when using the namespace "app" which may be mapped to any number
	 * of paths.
	 *
	 * @param array modules
	 */
	public static function modules(array $modules)
	{
		self::$modules = $modules;

		// namespace mapping
		self::$namespaces = \array_flip($modules);
		if (isset(self::$namespaces['app']))
		{
			// we consider the app value special, so it's invalid for our
			// namespace mapping
			unset(self::$namespaces['app']);
		}
		
		// compute paths;
		$paths = \array_keys($modules);
		self::$paths = array();
		foreach ($paths as $path)
		{
			self::$paths[] = \rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR
				. self::APPDIR.DIRECTORY_SEPARATOR;
		}
	}
	
	/**
	 * @return array paths
	 */
	public static function paths()
	{
		return self::$paths;
	}
	
	/**
	 * @return array namespae to path map
	 */
	public static function namespaces()
	{
		return self::$namespaces;
	}
	
	/**
	 * Appends extra paths to front of current paths.
	 * 
	 * @param array paths
	 */
	public static function add_frontpaths(array $paths)
	{
		$new_paths = $paths;
		foreach (self::$paths as $path)
		{
			$new_paths[] = $path;
		}
		self::$paths = $new_paths;
	}
	
	/**
	 * Appends extra paths to back of current paths.
	 * 
	 * @param array paths
	 */
	public static function add_backpaths(array $paths)
	{
		foreach ($paths as $path)
		{
			self::$paths[] = $path;
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
	public static function add_namespaces(array $namespace_paths)
	{
		foreach ($namespace_paths as $namespace => $path)
		{
			self::$namespaces[$namespace] = $path;
		}
	}
	
	/**
	 * @param string symbol name with namespace
	 * @return bool successfully loaded?
	 */
	public static function load_symbol($symbol)
	{	
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
			$target = DIRECTORY_SEPARATOR.\str_replace('_', DIRECTORY_SEPARATOR, $symbol_name).EXT;

			foreach (static::$modules as $path => $ns)
			{
				if (\file_exists($path.$target))
				{
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

			// didn't find the file
			return false;
		}
		else # non \app namespace
		{

			if (isset(self::$namespaces[$namespace]))
			{
				// Normally this file check wouldn't be required but we want to
				// support bridging for backwards compatiblity, which breaks
				// the normal logic of "unique namespaces".
				$file = self::$namespaces[$namespace].DIRECTORY_SEPARATOR
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

	/**
	 * Returns the first file in the file system that matches. Or false.
	 * 
	 * @param string relative file path
	 * @param string file extention
	 * @return bool|string path to file
	 */
	public static function file($file, $ext = EXT)
	{
		// append extention
		$file .= $ext;
		// find file
		foreach (self::$paths as $path)
		{
			if (\file_exists($path.$file))
			{
				// success
				return $path.$file;
			}
		}
		
		// failed
		return false;
	}
	
	/**
	 * @param string relative file path
	 * @param string file extention
	 * @return array files or empty array
	 */
	public static function file_list($file, $ext = EXT)
	{
		// append extention
		$file = $file.$ext;
		// find files
		$files = array();
		foreach (self::$paths as $path)
		{
			if (\file_exists($path.$file))
			{
				$files[] = $path.$file;
			}
		}
		
		return $files;
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
	public static function config($key, $ext = EXT)
	{
		// we assume configuration files are not dynamic
		static $loaded = array();
		
		// check if we didn't get asked for it last time
		if (isset($loaded[$key.$ext]))
		{
			return $loaded[$key.$ext]; 
		}
		else # not loaded
		{
			// we start at the bottom since we merge up
			$files = \array_reverse
				(
					self::file_list(self::CNFDIR.DIRECTORY_SEPARATOR.$key, $ext)
				);
			// merge everything
			$key .= $ext;
			$loaded[$key] = array();
			foreach ($files as $file)
			{
				self::config_merge($loaded[$key], (include $file));
			}

			// if there were no files this will be empty; which is fine
			return $loaded[$key];
		}
	}
	
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
	private static function config_merge(array & $base, array & $overwrite)
	{	
		foreach ($overwrite as $key => & $value)
		{
			if (\is_numeric($key))
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
					self::config_merge($base[$key], $value);
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
	
} # class
