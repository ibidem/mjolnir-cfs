<?php return array
	(
		'domain' => null,
		'path' => '/',

		'protocol' => 'http://', # eg. //, http://, https://
		'timezone' => 'Europe/London',
		'charset' => 'UTF8',
		'lang' => 'en-US',

		'development' => false,
	
		// Read & Write for owner, Read for everybody else
		'default.file.permissions' => 0644, 
	
		// Execution access is required to go into the directory
		'default.dir.permissions' => 0770,
	
	);
