Unlike classes configuration files don't overwrite each other, but instead
merge into each other. Associative arrays will get keys replaced by keys in
modules with higher priority, non-associative key arrays will get values
combined.

Here is a basic example. Given the following,

	<?php return array # in module1
		(
			'color' => 'red',
			'people' => [ 'John' => 'Plummer' ],
			'letters' => [ 'a', 'b', 'c' ],

		); # config

And the following:

	<?php return array # in module2
		(
			'date' => 'today',
			'color' => 'blue',
			'people' => [ 'John' => 'Carpenter', 'Anna' => 'Witch' ],
			'letters' => [ 'd', 'e', 'f' ],

		); # config

When we read the configuration in question we'll get:

	<?php return array
		(
			'date' => 'today',
			'color' => 'red',
			'people' => [ 'John' => 'Plummer', 'Anna' => 'Witch' ],
			'letters' => [ 'a', 'b', 'c', 'd', 'e', 'f' ],

		); # config

Higher priority overwrites lower priority.
