<?php return array
	(
		'mjolnir' => array
			(
				'idx' => 0,

				'title' => 'Mjölnir',

				'cover' => 'mjonir-logo.png',

				'authors' => array
					(
						// empty
					),

				'sections' => array
					(
						'mjolnir-cfs' => array
							(
								'idx' => 1,

								'namespace' => 'mjolnir\cfs',

								'title' => 'Cascading File System',

								'introduction' => array
									(
										'type'  => 'markdown',
										'file'  => '-00-Introduction.md',
									),

								'chapters' => array
									(
										'modules' => array
											(
												'idx'   => 1,
												'title' => 'Modules',
												'type'  => 'markdown',
												'file'  => '-01-Modules.md',
											),
										'loading-classes' => array
											(
												'idx'   => 2,
												'title' => 'Loading Classes',
												'file'  => '-02-Loading-Classes.md',
												'type'  => 'markdown',
											),
										'loading-files' => array
											(
												'idx'   => 3,
												'title' => 'Loading Files',
												'file'  => '-03-Loading-Files.md',
												'type'  => 'markdown',
											),
										'loading-configuration-files' => array
											(
												'idx'   => 4,
												'title' => 'Loading Configuration Files',
												'file'  => '-04-Loading-Configuration-Files.md',
												'type'  => 'markdown',
											),
										'composer-integration' => array
											(
												'idx'   => 5,
												'title' => 'Composer Integration',
												'file'  => '-05-Composer-Integration.md',
												'type'  => 'markdown',
											),
									)
							),
					),
			)
	);