<?php return array
	(
		/* Things to know about task configs...
		 *
		 *  - if a flag is not mentioned for the command it won't be passed
		 *  - configuration doubles as documentation
		 *  - A null value for a flag's default means it's mandatory.
		 *  - A non-null value means it's optional
		 *  - A false value means it's optional, but has no actual default value
		 *  - "toggle" is a special type for boolean flags, no need to pass value
		 *  - all "toggle" should have a default of false; using the flag => true
		 *  - if you do not specify a type, "toggle" is assumed
		 *  - if you do not specify a default, false is assumed
		 *  - each entry in the array of the command's description is a paragraph
		 *  - first entry in a command's description should be the oneline description
		 *  - flag types can be methods in any class; preferably the Task_Class itself
		 *  - you'll find general purpose tags in the Flags class
		 *
		 * If you need a command along the lines of:
		 *
		 *    minion some:command "something"
		 *    (meaning no flags)
		 *
		 * Just don't give it flags, handle it in the command's execution and explain it
		 * in the command's documentation (ie. description). Preferably use flags though
		 * and/or have that only as a shorthand and not as the only way.
		 */
		'status' => array
			(
				'category' => 'Inspection',
				'description' => array
					(
						'Perform basic environment checks.',
						'Modules should provide tests for all their dependencies, that ensure they will function properly in their environement.'
					),
				'flags' => array
					(
						'no-stop' => array
							(
								'description' => 'Do not stop on errors.'
							),
						'strict' => array
							(
								'description' => 'Treat failed as errors.'
							),
					),
			),
		'cleanup' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Cleans system. Optionally logs too.'
					),
				'flags' => array
					(
						'purge-logs' => array
							(
								'short' => 'l',
								'description' => 'Logs will also be deleted.'
							),
						'cache' => array
							(
								'short' => 'c',
								'description' => 'Only clean the cache.'
							),
					),
			),
		'compile' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Runs all +compile.rb scripts.'
					),
				'flags' => array
					(
						'local' => array
							(
								'description' => 'Only check sys.path/themes',
								'short' => 'l',
							),
					),
			),
		'config' => array
			(
				'category' => 'Inspection',
				'description' => array
					(
						'Print configuration.',
					),
				'flags' => array
					(
						'config' => array
							(
								'description' => 'configuration path',
								'type' => 'text',
								'short' => 'c',
							),
					),
			),
		'make:class' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Create class.'
					),
				'flags' => array
					(
						'class' => array
							(
								'description' => 'Class; with namespace.',
								'type' => 'text',
								'short' => 'c',
							),
						'category' => array
							(
								'description' => 'Class category.',
								'type' => 'text',
								'short' => 'g',
								'default' => false,
							),
						'with-tests' => array
							(
								'description' => 'Create tests.',
								'short' => 't',
							),
						'library' => array
							(
								'description' => 'Library class? (ie. not instantiatable)',
								'short' => 'l',
							),
						'forced' => array
							(
								'description' => 'Force file overwrites.'
							),
					),
			),
		'make:trait' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Create trait.'
					),
				'flags' => array
					(
						'trait' => array
							(
								'description' => 'Trait; with namespace.',
								'type' => 'text',
								'short' => 't',
							),
						'forced' => array
							(
								'description' => 'Force file overwrites.'
							),
					),
			),
		'make:module' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Create a basic module.'
					),
				'flags' => array
					(
						'name' => array
							(
								'description' => 'Name of module',
								'type' => 'text',
							),
						'namespace' => array
							(
								'description' => 'Namespace of module.',
								'type' => 'text',
								'short' => 'n',
							),
						'forced' => array
							(
								'description' => 'Force file overwrites.'
							),
						'mockup-template' => array
							(
								'description' => 'Fills in module for mocking.'
							),
						'sandbox-template' => array
							(
								'description' => 'Fills in module for sandbox testing.'
							),
					),
			),
		'versions' => array
			(
				'category' => 'Inspection',
				'description' => array
					(
						'Print version info, as defined by modules.'
					),
			),
		'licenses' => array
			(
				'category' => 'Inspection',
				'description' => array
					(
						'Tries to print license info, as defined by modules.',
						'Information issued by this command should only be used as a quick reference.',
						'The command will miss non-standard formatting for license files.'
					),
				'flags' => array
					(
						'namespace' => array
							(
								'description' => 'Namespace of target module.',
								'short' => 'n',
								'type' => 'text',
								'default' => false,
							),
					),
			),
		'honeypot' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Generate honeypot files.',
						'Honeypot files allow for IDEs to understand the app namespace and hence the connection between file hirarchies and calls.',
						'Namespace modules do not need honeypot files; attempting to generate one will result in errors. You should not have \app\Some_Namespace calls, as namespaces SHOULD always be final.'
					),
				'flags' => array
					(
						'namespace' => array
							(
								'description' => 'Namespace of target module.',
								'short' => 'n',
								'type' => 'text',
								'default' => '',
							),
						'prefix' => array
							(
								'description' => 'Namespace prefix.',
								'short' => 'p',
								'type' => 'text',
								'default' => '',
							),
						'verbose' => array
							(
								'description' => 'Print file info.',
								'short' => 'v',
							),
					),
			),
		'find:config' => array
			(
				'category' => 'Inspection',
				'description' => array
					(
						'List config files based on environment.',
						'eg. the path "version" will list all version files'
					),
				'flags' => array
					(
						'config' => array
							(
								'description' => 'Path to match cofing to.',
								'short' => 'c',
								'type' => 'text',
							),
					)
			),
		'find:file' => array
			(
				'category' => 'Inspection',
				'description' => array
					(
						'List files based on environment.',
						'eg. the path "config/version" will list all version files'
					),
				'flags' => array
					(
						'path' => array
							(
								'description' => 'Path to match files to.',
								'short' => 'p',
								'type' => 'text',
							),
						'ext' => array
							(
								'description' => 'File extention',
								'short' => 'e',
								'type' => 'text',
								'default' => EXT,
							),
					)
			),
		'find:class' => array
			(
				'category' => 'Inspection',
				'description' => array('List class files, based on class.', 'The namespace is assumed to be \app, as in \app\Some_Class.'),
				'flags' => array
					(
						'class' => array
							(
								'description' => 'Class name for which to find files on the system.',
								'short' => 'c',
								'type' => 'text',
							)
					)
			),
		'behat' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Search and execute all behat behaviour tests.',
						'You may pass any flags and they will be passed down to behat.',
					),
				'flags' => array
					(
						'feature' => array
							(
								'description' => 'Target a specific feature.',
								'type' => 'text',
								'default' => false,
							),
					),
			),
		'log:short' => array
			(
				'category' => 'Inspection',
				'description' => array
					(
						'Short log of system errors.',
						'logging -> short.log must be enabled.'
					),
				'flags' => array
					(
						'erase' => array
							(
								'description' => 'Erase log before opening.',
								'short' => 'e',
							)
					),
			),

		'make:phpunit' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Auto-generate any missing test classes for module.',
					),
				'flags' => array
					(
						'namespace' => array
							(
								'description' => 'Namespace',
								'short' => 'n',
								'type' => 'text',
								'default' => ''
							),
						'prefix' => array
							(
								'description' => 'Namespace prefix',
								'short' => 'p',
								'type' => 'text',
								'default' => ''
							),
					),
			),

		'phpunit' => array
			(
				'category' => 'Tools',
				'description' => array
					(
						'Run phpunit tests at the given path.',
					),
				'flags' => array
					(
						'path' => array
							(
								'description' => 'Path from where to detect tests.',
								'short' => 'p',
								'type' => 'text',
							),
						'unique' => array
							(
								'description' => 'Command will generate unique reports.',
								'short' => 'q',
							),
						'no-coverage' => array
							(
								'description' => 'Code coverage wont be generated.',
							),

						'stop-on-failure' => array
							(
								'description' => 'Stop execution on first failure.',
							),
					),
			),

	);
