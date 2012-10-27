<?php namespace app;

/// The default extension of resource files.
if ( ! \defined('EXT'))
{
	\define('EXT', '.php');
}

/// The directory in which your application specific resources are located.
if ( ! \defined('APPPATH'))
{
	\define('APPPATH', \realpath(DOCROOT.'+App').'/');
}

/// The directory in which the modules for the current project are located.
if ( ! \defined('MODPATH'))
{
	\define('MODPATH', \realpath(DOCROOT.'modules').'/');
}

/// The directory in which your plugins are located. (typically vendor)
if ( ! \defined('PLGPATH'))
{
	\define('PLGPATH', \realpath(DOCROOT.'vendor').'/');
}

/// The directory in which your mjolnir framework modules are located.
if ( ! \defined('MJLPATH'))
{
	\define('MJLPATH', \realpath(PLGPATH.'mjolnir').'/');
}

/// @see http://php.net/error_reporting
\error_reporting(-1); # report everything under the sun

/// @see http://php.net/spl_autoload_register
require \realpath(\dirname(__FILE__)).'/../CFS'.EXT;
\spl_autoload_register(['\mjolnir\cfs\CFS', 'load_symbol']);
\class_alias('\mjolnir\cfs\CFS', 'app\CFS');


# ---- Additional Configuration ---------------------------------------------- #

// Global exception handler should never be called. It is a global function and
// abusing it to manage your exception will get progressively more convoluted.
// Handling your exception on the spot in a try / catch or at an abstract level
// (ie. Layer_* level) is recomended. Sometimes some exceptions just slip
// though; especially in development. This function outputs a readable version
// of the message; assuming the environment is not borked.
\set_exception_handler('\mjolnir\exception_handler');

\set_error_handler('\mjolnir\error_handler');

\register_shutdown_function('\mjolnir\shutdown_error_checks');


# ---- Modules --------------------------------------------------------------- #

if (\defined('PUBDIR'))
{
	$pubdir_config = include PUBDIR.'config'.EXT;
}

$env_config = include DOCROOT.'environment'.EXT;

// setup the modules
CFS::modules($env_config['modules']);

// allow application to store and overwrite config files, routes, etc;
// everything except classes. You should always define your classes in
// appropriate modules in the MODPATH
CFS::frontpaths([APPPATH]);

// attempt to load private configuration
if (\defined('PUBDIR'))
{
	$pubdir_config = include PUBDIR.'config'.EXT;
	if (isset($pubdir_config['private.files']) && \file_exists($pubdir_config['private.files']))
	{
		CFS::frontpaths([$pubdir_config['private.files']]);
	}
}
else # console or other
{
	$base_config = include APPPATH.'config/mjolnir/base'.EXT;
	if (\file_exists($base_config['private.files']))
	{
		CFS::frontpaths([$base_config['private.files']]);
	}
}

// you are not suppose to overwrite namespaces and abstracts; that's a misuse.
// hence it makes no sense to search for them in \app\Class calls. Namespaces
// should always be explicit
CFS::namespacepaths($env_config['namespaces']);

$base_config = CFS::config('mjolnir/base');

// see: http://php.net/timezones
\date_default_timezone_set($base_config['timezone']);

// see: http://php.net/setlocale
\setlocale(LC_ALL, $base_config['locale.lang'].$base_config['charset']);


# ---- Composer Autoloaders -------------------------------------------------- #

if (\file_exists(PLGPATH.'/autoload'.EXT))
{
	// composer setup
	require PLGPATH.'/autoload'.EXT;
}

// cleanup
unset($env_config, $base_config);