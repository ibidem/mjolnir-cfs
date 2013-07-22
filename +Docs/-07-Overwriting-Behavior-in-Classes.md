We've talked about how to replace a class, but often times what you really want
is to replace functionality in a class rather then re-write the entire class.

To start with you'll first need to overwrite the class. For the sake of our
example we'll assume our class is `Example` as with the previous section, and
we're overwriting the class `demo\module3\Example` with `demo\module1\Example`.

If you replace the entire class your `demo\module1\Example` class will likely
look something like this:

	<?php namespace demo\module1;

	class Example
	{
		// empty

	} # class

Functional (sort of) but we've just thrown away all the functionality of the
previous class. So lets say we don't want to do that.

The first way we can pull functionality from the previous class back in is
by extending the other class directly. While we generally refer to classes
though the magic `app` namespace, we can also write the full namespace, so
writing the following will pull in the previous class into our class:

	<?php namespace demo\module1;

	class Example extends \demo\module3\Example
	{
		// empty

	} # class

This works but is a little inflexible. If say we were making a module and did
this then we have just said "our module needs to be the highest priority module
that extends the Example class" and "only our module can extend the Example
class," more or less (a module with knowledge of our module can circumvent
this limitation at the expense of it being unusable outside use with our module
so fat chance of that ever happening). So now lets do better:

	<?php namespace demo\module1;

	class Example extends next\Example
	{
		// empty

	} # class

Pay close attention to the syntax, it's NOT `\next\Example` it's `next\Example`,
ie. no slash before the special `next` keyword. Now we don't have any of the
previous problems. What we've done is tell the system we want to extend the
Example class that's next in line in module priority. So let's say we had
three modules (module1, module2, module3, in that order) with three copies of
the `Example` class (with obviously namespace on changed accordingly, and no
`extends` directive in the module3 version), when we access `\app\Example` we
would get the class `\demo\module1\Example` (since it's of the highest
priority) then have it extend `\demo\module2\Example` since due to
`next\Example` resolving to the next in line, then have that itself extend
`\demo\module3\Example` for the same reason. If we swaped our module2 with
module1 in the modules section of our `etc/environment.php` file we would then
get `\demo\module2\Example` extending `\demo\module1\Example` extending
`\demo\module3\Example` with out making any file changes.

The last way to extend the class is though partial namespace resolution, this
is useful sometimes but generally you'll want to use `next\Class` unless you
have a really good reason to be specific. The way partial namespace resolution
works is since when extending a class you're generally only interested in the
class and not the namespace segments of the class (other then the main one) you
can just extend a class with the main segment. So take the class
`\mjolnir\access\User` you can write:

	<?php namespace demo\module1;

	class Example extends \mjolnir\access\User
	{
		// empty

	} # class

Or, you can omit the module namespace segments and just write:

	<?php namespace demo\module1;

	class Example extends \mjolnir\User
	{
		// empty

	} # class

This makes your class a little bit more robust, if User is moved to a different
namespace your code will still work, but unless you *really need to be
specific* you're better off with the `next\Class` method.
