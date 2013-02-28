<?php return array
	(
		'mjolnir\cfs' => array
			(
				 'PHP 5.4.10 (or higher)' => function ()
					{
						if (PHP_VERSION_ID >= 50410)
						{
							return 'available';
						}

						return 'error';
					},
				'NOT broken PHP: 5.4.11, 5.4.12' => function ()
					{
						if (PHP_VERSION_ID != 50411 && PHP_VERSION_ID != 50412)
						{
							return 'available';
						}

						return 'error';
					}
			),
	);
