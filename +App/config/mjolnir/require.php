<?php return array
	(
		'mjolnir\cfs' => array
			(
				 'PHP 5.4.10 (or higher)' => function ()
					{
						if (PHP_VERSION_ID >= 50410)
						{
							return 'satisfied';
						}

						return 'error';
					},

				'tested PHP version (5.4.13 or lower) or 5.4.16+' => function ()
					{
						if (PHP_VERSION_ID <= 50413 || PHP_VERSION_ID >= 50416)
						{
							return 'satisfied';
						}

						return 'failed';
					},
				'stable PHP (unstable versions: 5.4.11, 5.4.12)' => function ()
					{
						if (PHP_VERSION_ID != 50411 && PHP_VERSION_ID != 50412)
						{
							return 'satisfied';
						}

						return 'error';
					},
				'development=false' => function ()
					{
						return \app\CFS::config('mjolnir/base')['development'] == false ? 'satisfied' : 'failed';
					},
				'system email' => function ()
					{
						return \app\CFS::config('mjolnir/base')['system']['email'] !== null ? 'satisfied' : 'failed';
					},
				'system title' => function ()
					{
						return \app\CFS::config('mjolnir/base')['system']['email'] !== 'Untitled' ? 'satisfied' : 'failed';
					},
			),
	);
