<?php namespace mjolnir\cfs;

// make sure EXT is defined
if ( ! \defined('EXT'))
{
	\define('EXT', '.php');
}

/**
 * @author  Ibidem Team
 * @version 1.0
 */
interface CFSCompatible
{
	/**
	 * Default path for (non-autoloadable-class) files in modules.
	 */
	const APPDIR = '+App';

	/**
	 * Default path for configuration files in modules. Configurations are
	 * files that return arrays and are merged togheter before returned.
	 *
	 * This path is relative to APPDIR
	 */
	const CNFDIR = 'config';

	// ------------------------------------------------------------------------
	// Inspection & Loading

	/**
	 * @param string symbol
	 * @param boolean autoload while checking?
	 * @return boolean symbol exists as class, interface, or trait?
	 */
	static function symbol_exists($symbol, $autoload = false);

	/**
	 * @param string symbol name with namespace
	 * @return bool successfully loaded?
	 */
	static function load_symbol($symbol);

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
	static function modules(array $modules);

	/**
	 * @param array modules
	 */
	static function frontmodules(array $modules);

	/**
	 * @param array modules
	 */
	static function backmodules(array $modules);

	/**
	 * Specifies some special namespaces that are not suppose to map as modules.
	 * A very simple example of this are interface modules. Interfaces are
	 * suppose to be unique; you're not suppose to overwrite them. So it makes
	 * no sense to search for them as modules; wasted checks.
	 *
	 * @param array namespace paths
	 */
	static function namespacepaths(array $namespace_paths);

	/**
	 * Appends extra paths to front of current paths.
	 *
	 * @param array paths
	 */
	static function frontpaths(array $paths);

	/**
	 * Appends extra paths to back of current paths.
	 *
	 * @param array paths
	 */
	static function backpaths(array $paths);

	// ------------------------------------------------------------------------
	// Paths Retrieval

	/**
	 * Returns the first file in the file system that matches. Or null.
	 *
	 * @param string relative file path
	 * @param string file extention
	 * @return string path to file; or null
	 */
	static function file($file, $ext = EXT);

	/**
	 * Retrieve a list of all files on the system matching the criteria.
	 *
	 * @param string relative file path
	 * @param string file extention
	 * @return array files (or empty array)
	 */
	static function file_list($file, $ext = EXT);

	/**
	 * Retrieves the module's root path.
	 *
	 * @param string namespace
	 * @return string path
	 */
	static function modulepath($namespace);

	/**
	 * Retrieves the module's class path. Typically this is merely the root of
	 * the module.
	 *
	 * @param string namespace
	 * @return string class path
	 */
	static function classpath($namespace);

	/**
	 * Retrieves the path the files for the given namespace, ie. +App/ path
	 * relative to the module's root.
	 *
	 * @param string namespace
	 * @return string file path
	 */
	static function filepath($namespace);

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
	static function dir($directory);

	// ------------------------------------------------------------------------
	// Configuration Files

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
	static function config($key, $ext = EXT);

	/**
	 * Same as config, only it explicitly only handles files. Which is to say
	 * it won't search the database (if present) for the configuration.
	 *
	 * @param string configuration key (any valid file syntax)
	 * @return array configuration or empty array
	 */
	static function config_file($key, $ext = EXT);

	// ------------------------------------------------------------------------
	// System Information

	/**
	 * Current module declarations.
	 *
	 * @return array
	 */
	static function system_modules();

	/**
	 * @return array all known paths
	 */
	static function paths();

	/**
	 * @return array namespace to path map
	 */
	static function namespaces();

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
	static function config_merge(array & $base, array & $overwrite);

	/**
	 * Applies config_merge, but returns array and doesn't alter base.
	 *
	 * @param array base
	 * @param array overwrite
	 * @return array merged configuration
	 */
	static function merge(array $base, array & $overwrite);

	// ------------------------------------------------------------------------
	// Utility

	/**
	 * Sets local persistent storage object to use when retrieving
	 * configurations files. The object should be preconfigured.
	 *
	 * Passing null will remove the component.
	 *
	 * @param \mjolnir\types\Storage
	 * @param string key that identifies configuration name (no EXT)
	 * @param string key that identifies serialized object
	 */
	static function storage	(
			\mjolnir\types\Storage $storage = null,
			$config_key = 'config',
			$value_key = 'serialized'
		);

	/**
	 * Cache object is used on symbol, configuration and file system caching. Or
	 * at least that's the intention.
	 *
	 * Passing null will remove the component.
	 *
	 * @param \mjolnir\types\Cache preconfigured cache object
	 * @param int duration for files
	 * @param int duration for configs
	 */
	static function cache (
			\mjolnir\types\Stash $cache = null,
			$file_duration = 1800 /* 30 minutes */
		);

} # interface