<?php namespace app; return array
/////// Access Protocol Configuration //////////////////////////////////////////
(
	'whitelist' => array # allow
		(
			'+admin' => array
				(
					Allow::backend
						(
							'filepermissions'
						)
				),
		),
	
); # config