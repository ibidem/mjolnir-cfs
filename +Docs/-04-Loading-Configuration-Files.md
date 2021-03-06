To load a configuration "file" the function `CFS::config($key, $ext = EXT)` is
used. In certain extreme cases you may want to explicitly make sure
the configuration you are loading is coming from a physical file (and not
something else, such as a database) in which case you would use
`CFS::configfile($key, $ext = EXT)`.

By default configuration files are mere PHP files which return an array. If
required a configuration file may be externally loaded via an include as
follows:

	$config = include 'path/to/configuration.php';

This however will rarely be equivalent to the result of
`CFS:config('path/to/configuration')` due to how configurations are managed.

In the cascading file system the values for a configuration file is obtained as
follows.

 1. the system will search for all configuration files in all modules; more
 specifically the search will match the given pattern to `+App/config` of all
 modules, and just `config` for any explicit paths (such as private files).
 2. the resulting arrays will be recursively merged starting from the values
 obtained from the bottom modules and going up. So values you place in top
 modules will always overwrite values in lower modules.
 3. if no configuration files were present an empty array is returned

So the value of a single configuration file is not necessarily representative of
the complete result.

Typically you will place defaults in the module which implements the
configuration and overwrite as needed in the modules that use the configured
implementation.

Because configuration files are plain old PHP code, you can have any amount of
complexity in it. Here are just a few examples:

You can generate a configuration dynamically; for example if www path is
not defined you may attempt to resolve the configuration to some other more
useful value; remember that the configuration files are still plain old PHP
files so there is very little limitation on what processing they can do.

You can split the configuration into a series of arrays and simply return the
merged output; for example in the case of a script configuration, you can form
small manageable arrays with points of interest (form helpers, modals, etc)
then merge them and remove duplicates. You thus avoid having monolithic
declarations, have an easy mechanism to dealing with script duplication, and
best of all: it's far more maintainable.

You can use variables for cleaner syntax; for example in routing, with the
exception of certain abstract patterns, you often have to define various
repeating patterns for said routes, you can use variables to avoid this, which
is extremely useful when dealing with 40+ routes (as is the case a lot of the
time). Example:

	<?php

	// segments
	$id = ['id' => '[0-9]+'];
	$slug = ['slug' => '[a-z0-9-]+'];

	// mixins
	$resource = '<id>/<slug>(/<action>)';

	// access
	$control = ['GET, 'POST'];

	return array
		(
			"/example/{$resource}"
				=> [ 'example', $id + $slug + ['action' => '(insert)'], $control ],
		);

You can place closures within configuration files allowing you to create
a dynamic collection of them for easy management. For example url generators,
such as a thumbnail or action urls for forms, a closure for generating the
correct path for a given filename saves space and is very flexible.

You can translate the configuration from an external 3rd party source
directly in the configuration file and output it; this means that
if the source configuration is updated your configuration is updates as well;
which is useful for capturing changes to defaults or extra options that become
available; this may be a json, yaml, another php file, etc, or if necessary the
application might even resort to going to the web to get updates (eg. list of
countries, cities, etc), regardless of format, or changes in the format, when
you need the configuration you merely perform a standard call.

*Configuration files are resolved once. Any subsequent calls to `CFS::config`
with the same parameters merely results in the previous (cached) result. This
means you can abuse calls, but it also means you should treat values from
configuration files as static. A "timer" value will not update for example; but
you can always use a closure within the configuration for those cases.*

If you wish to cache the resolved configuration file between requests you can
add a `@cfs` key to the configuration file which will be read by the system post
merge and settings parsed. By passing cachable the system will be instructed to
persist the configuration values between requests. The values must be
serializable for the values to be persisted so if your configuration has
functions defined avoid making the configuration cachable.

Example configuration using `@cfs`,

	<?php return array
		(
			'@cfs' => array
				(
					'cachable' => true,
				),

			'example' => 12,

		); #config

Accessing the configuration will return `[ 'example' => 12 ]` in this case.

If you need the `@cfs` key, simply write it as `\@cfs`.

Please **DO NOT** store security keys, passwords and other sensitive information
in configuration files located in your source repositories. Not only is it a
security liability, but it is also a pain for any development outside your
production server (unless all your test servers, along with every site you
ever built somehow has the same keys; which would be nonsensical).

The correct way of dealing with sensitive configuration entries is to place them
in a separate file path that sits at the top of the cascading file system and
outside your `DOCROOT`. The
[mjolnir-template-app](https://github.com/ibidem/mjolnir-template-app.git) shows
an example of this: you specify the path to the private files via a
`private.files` entry in `WWWPATH/config.php` and a `DOCROOT/.key.path` file
for CLI access. The `DOCROOT/.key.path` is ignored via your `.gitignore` and
merely contains a path.
