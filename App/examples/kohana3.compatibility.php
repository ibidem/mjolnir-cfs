<?php namespace app;

// This is example how to use Kohana3 modules. Simply correctly define bridges 
// using the example as a guide, for the module(s) in question. You can then use 
// the modules as if they were up to date and supported namespaces.
//
// The code bellow should be after the default autoloader.


# ---------- Copy AS-IS in your environment.php and it will work :) ---------- #
  
// load the bridge autoloader
require PLGPATH.'kohana4'.$ds.'cfs'.$ds.'Kohana3'.$ds.'Bridge.php';

// define a global alias for it to app
\class_alias('\\kohana4\\cfs\\Kohana3_Bridge', 'app\\Kohana3_Bridge');

// setup the bridges
Kohana3_Bridge::bridges
	(
		array
		(
			PLGPATH.'some_kohana3_module' => array
				(
					'namespace' => 'proper\\namespace',
					'prefix' => 'moduleprefix',
				),
		)
	);

// include the paths in the CFS (note: this is not the same as adding modules)
CFS::add_backpaths(Kohana3_Bridge::paths());

// register the autoloader
\spl_autoload_register(array('\\kohana4\\cfs\\Kohana3_Bridge', 'load_class'));