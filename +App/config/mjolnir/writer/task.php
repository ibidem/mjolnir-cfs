<?php return array
	(
		// [!!] enabling highlighting on windows (which doesn't support it) will
		// work, but the workarounds to get it to work may cause slowdown
		// [!!] with highlighting on stderr output is not guranteed when using
		// the error method; relevant when buffering
		'highlighting' => false,
		// maximum width of the console window
		'width' => 80,
		// highlight mappings; can be colors or anything
		'highlight' => array
			(
				'Black'       => '0;30',
				'Red'         => '0;31',
				'Green'       => '0;32',
				'Blue'        => '0;34',
				'Purple'      => '0;35',
				'Cyan'        => '0;36',
				'White'       => '0;37',
				'Brown'       => '0;33',
				'LightGray'   => '0;37',
				'DarkGray'    => '1;30',
				'LightBlue'   => '1;34',
				'LightGreen'  => '1;32',
				'LightCyan'   => '1;36',
				'LightRed'    => '1;31',
				'LightPurple' => '1;35',
				'Yellow'      => '1;33',
			)
	);
