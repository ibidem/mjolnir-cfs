<?php namespace app;

/**
 * The directory in which your application specific resources are located.
 */
if ( ! \defined('APPPATH'))
{
	\define('APPPATH', \realpath(DOCROOT.'App').DIRECTORY_SEPARATOR);
}

/**
 * The directory in which the modules for the current project are located.
 */
if ( ! \defined('MODPATH'))
{
	\define('MODPATH', \realpath(DOCROOT.'modules').DIRECTORY_SEPARATOR);
}

/**
 * The directory in which your plugins are located.
 */
if ( ! \defined('PLGPATH'))
{
	\define('PLGPATH', \realpath(DOCROOT.'plugins').DIRECTORY_SEPARATOR);
}

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @see  http://kohanaframework.org/guide/about.install#ext
 */
\define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @see  http://php.net/error_reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
\error_reporting(E_ALL | E_STRICT);

/**
 * Setup the autoloader
 * 
 * @see  http://php.net/spl_autoload_register
 */
require \realpath(\dirname(__FILE__)).$ds.'..'.DIRECTORY_SEPARATOR.'CFS'.EXT;

\spl_autoload_register(array('\\ibidem\\cfs\\CFS', 'load_symbol'));

\class_alias('\\ibidem\\cfs\\CFS', 'app\\CFS');