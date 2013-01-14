<?php return array
	(
		'file' => function ($idx, array $argv)
			{
				return $argv[$idx+1];
			},

		'text' => function ($idx, array $argv)
			{
				return $argv[$idx+1];
			},

		'path' => function ($idx, array $argv)
			{
				return $argv[$idx+1];
			}

	);
