To overwrite a class it's very simple: you just create a class in a higher
level module.

For example, let's say your `etc/environment.php` defines the following
modules:

	$modpath.'module1' => 'demo\module1',
	$modpath.'module2' => 'demo\module2',
	$modpath.'module3' => 'demo\module3',

In this configuration `module1` has the highest priority and `module3` has
the lowest priority. Or if we go by namespace we can say `demo\module1` is
configured to be of higher priority then `demo\module2` which is of higher
priority then `demo\module3`.

Lets say we have a class `demo\module3\Example` already defined. When we access
`\app\Example` the system will resolve the class to the highest priority module
and since it can not find the class in module1 and can't find the class in
module2, the module3 version of the class will get loaded.

If we define a class `demo\module1\Example` however, since it's in a module of
higher priority when we call `\app\Example` we'll actually get the module1
version now instead of the module3 version.

That's all there is to it.

Remember that the system may cache paths for fast resolution so if you're
testing and getting the wrong version just run a `order cleanup` to flush out
the caches.
