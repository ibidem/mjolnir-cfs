<?php return array
	(
		'domain' => null,
		'path' => '/',

		'timezone' => 'Europe/London',
		'charset' => 'UTF8',
		'locale.lang' => 'en_US',
		'default.protocol' => 'http://', # eg. //, http://, https://

		'development' => false,
	
		// Read & Write for owner, Read for everybody else
		'default.file.permissions' => 0644, 
	
		// Execution access is required to go into the directory
		'default.dir.permissions' => 0770,
	
	);
