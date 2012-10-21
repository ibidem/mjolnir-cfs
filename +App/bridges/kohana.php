<?php namespace app;

/**
 * How to use:
 *
 *		$kohana = array
 *			(
 *				PLGPATH.'kohana3/core' => array
 *					(
 *						'namespace' => 'kohana3\core',
 *						'prefix' => 'kohana',
 *					),
 *			);
 *
 *		include $cfspath_files.'bridges/kohana'.EXT;
 *
 */

\define('SYSPATH', true); # kohana3 file-safeguard requirement

// setup the bridges
\mjolnir\cfs\Kohana3_Bridge::bridges($kohana);

// include the paths in the CFS (note: this is not the same as adding modules)
CFS::backpaths(\mjolnir\cfs\Kohana3_Bridge::paths());

// register the autoloader
\spl_autoload_register(['\mjolnir\cfs\Kohana3_Bridge', 'load_class']);