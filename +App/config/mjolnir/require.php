<?php return array
	(
		'mjolnir\cfs' => array
			(
				 'PHP 5.4 or higher' => function ()
					{
						if (PHP_VERSION_ID >= 50400)
						{
							return 'available';
						}

						return 'error';
					}
			),
	);
