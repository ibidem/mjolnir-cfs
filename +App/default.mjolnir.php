<?php namespace app;

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

/// The default extension of resource files.
if ( ! \defined('EXT'))
{
	\define('EXT', '.php');
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
