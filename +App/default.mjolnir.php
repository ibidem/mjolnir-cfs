<?php namespace app;

	#
	# This configuration is a quick default; the environment setup will use the
	# default environment, typically 'main' if no other overwrites have
	# happened.
	#

	// default extension
	\defined('EXT') or \define('EXT', '.php');


	# ---- Error Reporting --------------------------------------------------- #

	// @see http://php.net/error_reporting
	if (isset($wwwconfig))
	{
		\error_reporting($wwwconfig['error-reporting']);
	}
	else # default
	{
		\error_reporting(-1);
	}

	// load logging and error reporting
	$thisdir = \realpath(__DIR__).'/';
	include $thisdir.'functions/mjolnir/logging'.EXT;
	include $thisdir.'functions/mjolnir/errors'.EXT;

	// Global exception handler should never be called. It is a global function
	// and abusing it to manage your exception will get progressively more
	// convoluted. Handling your exception on the spot in a try / catch or at
	// an abstract level (ie. Layer_* level) is recomended. Sometimes some
	// exceptions just slip though; especially in development. This function
	// outputs a readable version of the message; assuming the environment is
	// not borked.
	\set_exception_handler('\mjolnir\exception_handler');
	\set_error_handler('\mjolnir\error_handler');
	\register_shutdown_function('\mjolnir\shutdown_error_checks');


	# ---- Autoloading ------------------------------------------------------- #

	// setup additional helper temporary globals
	$etcpath = \realpath($syspath.'etc').'/';
	$modpath = \realpath($syspath.'modules').'/';
	$vdrpath = \realpath($syspath.'vendor').'/';
	$mjpath  = \realpath($vdrpath.'mjolnir').'/';

	$envfilepath = \realpath($etcpath.'environment'.EXT);

	// Composer
	if (\file_exists($vdrpath.'/autoload'.EXT))
	{
		// composer setup
		require $vdrpath.'/autoload'.EXT;
	}

	// Mjolnir
	require \realpath(__DIR__).'/../CFS'.EXT;
	\spl_autoload_register(['\mjolnir\cfs\CFS', 'load_symbol']);
	\class_alias('\mjolnir\cfs\CFS', 'app\CFS');

	$envconfig = include $envfilepath;

	// setup the modules
	CFS::modules($envconfig['modules']);

	// allow application to store and overwrite config files, routes, etc;
	// everything except classes. You should always define your classes in
	// appropriate modules in the module path
	CFS::frontpaths([ $etcpath ]);

	// attempt to load private configuration; console applications and other
	// types are responsible for loading it on their own
	if (isset($wwwconfig, $wwwpath))
	{
		\app\Env::set('www.config', $wwwconfig);

		if (isset($wwwconfig['key.path']) && \file_exists($wwwconfig['key.path']))
		{
			CFS::frontpaths([ $wwwconfig['key.path'] ]);
		}
		else if ($wwwconfig['key.path'] !== null)
		{
			if ($wwwconfig['development'])
			{
				echo "The key path [{$wwwconfig['key.path']}] does not exist.";
			}
			else # public error
			{
				include $wwwpath.'500'.EXT;
			}

			exit(1);
		}
		else # not specified
		{
			if ($wwwconfig['development'])
			{
				echo "Key files not specified (null value).";
			}
			else # public error
			{
				include $wwwpath.'500'.EXT;
			}

			exit(1);
		}
	}

	// you are not suppose to overwrite namespaces and abstracts; that's a misuse.
	// hence it makes no sense to search for them in \app\Class calls. Namespaces
	// should always be explicit
	CFS::namespacepaths($envconfig['namespaces']);


	# ---- Environment Setup ------------------------------------------------- #

	// retrieve default configuration
	$env = Environment::instance();

	// system root
	$env->set('sys.path', $syspath);
	// misc files and general purpose environement configuration files
	$env->set('etc.path', $etcpath);
	// application modules
	$env->set('modules.path', $modpath);
	// packaged modules
	$env->set('vendor.path', $vdrpath);
	// your module configuration
	$env->set('environment.config', $envconfig);
	// mjolnir modules
	$env->set('mjolnir.path', $mjpath);
	$env->set('drafts.path', \realpath($syspath.'drafts').'/');

	$baseconfig = CFS::config('mjolnir/base');

	// see: http://php.net/timezones
	\date_default_timezone_set($baseconfig['timezone']);

	// see: http://php.net/setlocale
	\setlocale(LC_ALL, \app\Lang::idlang($baseconfig['lang']).$baseconfig['charset']);


	// cleanup
	unset
	(
		$thisdir,

		$etcpath,
		$modpath,
		$vdrpath,
		$mjpath,

		$envfilepath,

		$env,
		$envconfig,
		$baseconfig
	);