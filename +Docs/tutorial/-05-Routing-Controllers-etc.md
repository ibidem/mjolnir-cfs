We'll start by opening `~/etc/config/routes.php`. You should see:

	<?php return array
		(
			'/'
				=> [ 'home.public' ],
		);

The "public" in "home.public" specifies what layer stack it uses (it's still
part of the name mind you). The "public" stack is one of the default stacks and
is essentially: HTTP, Access, HTML, Theme, MVC, in that order. We'll talk about
creating your own stack when talking about creating an api. For reference,
other default stacks are: log, html, raw, jsend, json, csv, resource (depending
on your modules you may have more).

The file you are viewing is part of the "routing system," there is also a
"relay system" (the routing system has priority in processing). Both achieve
the same function but the relay system is a more advanced (and verbose) version
that you should be using when you want to specify routes in modules. All routes
in the routing system count as relays. The routing system is just a very space
efficient way to write them; since the main application tends to have a lot.

The `'/'` is the pattern that's matched (ie. "the route"), in this case the route
is the root of the site, ie. `127.0.0.1/demo/`. Here's some more pattern
examples: `'/'`, `'/home'`, `'/people/person/<id>'`,
`'/<organization>/employees(/<action>)'`. Words in angled brackets are route
parameters, they are processed when the route matches and are available in the
controllers. Parentheses specify optional components; a url with out the part
in parentheses will still match the route.

The array pointed to by the url pattern is the route's configuration, this
consists of in order:

 * **the route name**; this is also get resolved to a controller class. If you
   wish to have the name merely be an alias you can write the route with the
   following syntax: `'/' => [ [ 'alias-route.public' => 'actual.public' ] ]`,
   note how the name is an array now instead of a string. The name of the route
   is mandatory.

 * **the route parameters**; can be omitted if you don't have parameters. It's
   generally in the form of `'/<id>' => [ 'example.public', [ 'id' => '[0-9]+' ] ]`

 * **the route methods**; if omitted will be interpreted as `['GET', 'POST']`

For our purposes we are going to change `home.public` to `landing.public`. You
should now have this file:

	<?php return array
		(
			'/'
				=> [ 'landing.public' ],
		);

We now need to give access rights to this route. Open
`~/demo/0.1.x/etc/config/mjolnir/access.php`. This is the main access
control file.

The access system does not work based on ACLs, and will run perfectly fine on
it's own with no database access; assuming you don't need users. Due to it not
requiring database access you can perform a lot of very complex "can"
operations. All that said, you may create ACLs and any other system you desire
though the use of Protocol classes; we are mostly going to keep it simple and
not have database dependencies so we'll use vanilla protocols (ie. built-in
default helpers that come with the library).

In the `whitelist` part of the file define the following rule:

	Auth::Guest => array
		(
			Allow::relays
				(
					'landing.public'
				)
				->unrestricted(),
		),

`Auth::Guest` here gives us the guest account (ie. anonymous visitors, or
anyone not logged in). `Allow::relays` is a variable parameter function that
returns a `Protocol` object allowing entries from a specific relay (we could
simply instantiate the object in question, this is just more readable). As
mentioned our route `landing.public` also counts as a relay.

The method `unrestricted()` configures the object to ignore context. Normally,
`landing.public` with the context of `['action' => 'index']` is not equivalent
to `landing.public` with no context, and so we would have to specify every
possible value for the action (for clarity we're not going to do this and just
allow any parameter since it's perfectly fine in this case).

It's important to know that the access system will DENY until specified
otherwise, not the other way round. So, as long as you're specific it's
impossible to ALLOW by mistake. Similarly unless you create a protocol in the
`Auth::Guest` section (highly unlikely) it's very hard to accidentally give
access to anonymous visitors.

You can enable and view `order log:access` to see access resolution as it's
happening in case you're dealing with access errors. We won't cover that here
though.

You can create your own custom classes. For example, `Protocol_VaultAccess`
might only allow access if it's a specific time of day, day of week (work day)
and not a holiday. Or a `Protocol_Members_ACL` might call on the database for
information to determine the users access (ACLs are only "better" when you need
to have options for customizing access inside the application; every other case
you're better off with no dependency on the database). Since it's programmatic
you can also do things like grant access to an user based on the user's
relationship to someone else. So if X is in a Division with Y then X should have
access to the project Y is currently working on; this is simpler and more
intuitive then granting and removing privileges to X for the project which
would require checks and operations when project is created, assigned, Y is
assigned/unassigned to a project, X is assigned/unassigned to Y and all sorts
of other relationship concerns.

Before continuing please open another console instance and run the following
command:

	clear ; order log:short --erase

We'll refer to this from now on as the "error console." It's a good idea to have
it open at all times in development, any errors that happen in the background
will appear here even if while navigating the application you may not notice
them; note that some errors can be hidden by html markup such as an error that
occurs inside a tag attribute value, so even the page you see may have errors
in it.

The `--erase` (short form: `-e`) parameter tells the `log:short` task to throw
away the previous log (this avoids confusion).

Now open `127.0.0.1/demo/` again.

In the error log window you should see an entry with
`Class '\app\Controller_Landing' not found`. The error is telling us our route works and our access rule works but we don't
have a controller yet.

If you see the backend, you're still logged in with the admin account (user
roles can have custom "dashboard" pages to which users are redirected to; the
admin panel is the default for the admin role), please sign out and open the
specified link again.

We'll first need to create a module for our controller.

	order make:module --name core --namespace 'demo\core'

In the file `~/demo/0.1.x/etc/environment.php` add the new module you've just
created to the modules section at the very top. The line you need to write will
be specified by the command above upon successful creation, and will look like
this:

	$modpath.'core' => 'demo\core',

We can now create the class.

	order make:class -c 'demo\core\Controller_Landing'

You can very well just create the class by hand but this tends to be better
since it checks for the namespace, fills in comments, updates honeypot files,
may fill in the class with placeholders, adds a `@todo` comment, etc.

We're going to create a very standard and easy looking controller, you do have
the option to make the controller anything you need.

Open the new file `~/demo/0.1.x/modules/core/Controller/Landing.php`. If you're
confused on the path please review the Cascading File System section of the
documentation for the very basics of the file system's inner workings.

You should now change the extended class to `\app\Controller_Base`.
`Controller_Base` is a shorthand; it's essentially extending `Puppet`,
implementing the `Controller` interface and using the `Controller` trait, which
more or less in plain english means it's a special generic controller that has
all the traits of a controller but also happens to have a name and allow for
operations based on it's name (basically you can call functions such as
`codename` or `codegroup` and others, to do meta programming inside it).

You class should should look like this:

	class Controller_Landing extends \app\Controller_Base

We will now create the main trait,

	order make:trait -t 'demo\core\Trait_Controller_DemoCommon'

In the lifetime of a application we'll be creating more then one controller,
it's useful to have at least one common trait so we can share functionality
between them; we don't use a base class since we want to have the option of
extending different types of controllers as well. Complex class hierarchies are
also harder to maintain then trait hierarchies.

Inside the body of the `Controller_Landing` class add the following declaration:

	use \app\Trait_Controller_DemoCommon;

Now any methods we add in the trait will be injected in the class, so long as
we don't create a method with the same name in the class, in which case the
trait method will be overwritten by the class method.

Now open the new trait file:
`~/demo/0.1.x/modules/core/Trait/Controller/DemoCommon.php`

A common requirement of many controllers is the index action, it's typically
the same functional code or if it's more complex typically it delegates to some
other classes to resolve so we can create a default one in our trait.

We explained earlier how in `'landing.public'` the public is the name of the
stack we execute. The convention with routes (relays can do whatever) is that
the name of the stack used is also the prefix of the action (implied
underscore). Another convention is that unless specified as a parameter in the
url (ie. if the route is `/something(/<action>)` then `/something/test` has the
action `test`) the default action (be it if there is a `<action>` segment or
not) is always "index." So given we don't specify even an `<action>` segment
the action is always `public_index`. If we weren't using the "public" stack and
instead using say a custom "api" stack the action would be instead `api_index`.

With regard to controllers and data flow, the controller is expected to return
a value that can be interpreted by the layers in it's stack, in the case of the
"public" stack that value must be either a `string` or a object implementing
the `\mjolnir\types\Renderable` interface (see types section). An api stack on
the other hand might require you always return a PHP `array`.

Please add the following `public_index` method:

	/**
	 * @return \mjolnir\types\Renderable|string
	 */
	function public_index()
	{
		return 'hello, world';
	}

If you now open `127.0.0.1/demo/` you should see "hello, world." If you don't
please check your error console, you may have a typo or some other error.

Above is the basic example, let's do something more complex; again remember you
can always have your own way of doing things, there are only some basic
interface requirements (which if you wish you can get rid of by not using the
"public" stack). Replace the above `public_index` method with the following:

	/**
	 * @return \mjolnir\types\Renderable|string
	 */
	function public_index()
	{
		$this->channel()->set('title', 'Demo');

		return \app\ThemeView::fortarget(static::dashsingular(), \app\Theme::instance())
			->pass('control', $this)
			->pass('context', $this);
	}

Back in the `Controller_Landing` class add `static $grammar = [ 'landing' ];`,
this will allow for the little bit of meta programming that's happening with
the `static::dashsingular()` method above. Your `Controller_Landing` class
should look something like this:

	class Controller_Landing extends \app\Controller_Base
	{
		use \app\Trait_Controller_DemoCommon;

		static $grammar = [ 'landing' ];

	} # class

If you now open the site you should see in your error log a message containing
the following: `Theme Corruption: undefined target`. This error is telling you
that the "theme target" you tried to access (ie. "landing") doesn't exist. In
our case the target in the theme corresponds to the controller name because we
chose to have it this way, but in general you can have the theme targets be
whatever. This means that using a basic mockup controller you can mockup an
entire site and work on the style and layout independent of the site's
functional code.

We'll only cover basics to working with themes.

First, go to the theme configuration file:
`~/demo/0.1.x/themes/classic/+theme.php`

Now add the following in the "mapping" section of the file:

	'landing' => [ 'landing' ],

This tells the theme system that you want to resolve the target "landing" using
the given array of files; in our case just the one "landing" file. The file
paths are resolved from the root of the theme and if we had provided multiple
files they would have been placed one in another.

Now create the file `~/demo/0.1.x/themes/classic/landing.php`

Based on how we've written our `public_index` method we now have access to two
variables `$control` and `$context`. `$context` is in general the visible user
recognizable data on the page, while `$control` is the meta-data on the page
(and almost always heavily tied into the controller, hence the name). Things
like the action of a form, the state of the page (editable, non-editable) or
some other details (what kind of page it is, theme options, etc) generally fall
in the category of page meta-data and will be accessed via `$control`. In our
case both $context and $control point to the same object, a `Controller_Landing`
instance; sometimes it's useful to split contexts outside of the scope of the
controller so you can have multiple contexts compose into a single context that
you feed to the page.

We are not going to bother with `$control` and `$context` for now, please just
write "hello, theme" inside the file.

If you now re-open the site you should see "hello, theme." If you don't, as
before, please check your error console for typos and other errors.

One thing you probably have not noticed is that the entire output has been
wrapped in the correct html (even when you just returned "hello, world" from
the controller earlier), this is due to the html layer. If you do not have
very good understanding of the theme system you are advised to avoid using a
stack with out the html layer or a stack with out a compatible
drop-in-replacement of the html layer, since it does a lot more then just wrap
your content in correct html meta.

We will now continue with this basic static site example by adding a method to
display the "hello world" message.

Add the following method in your `Controller_Landing` class:

	/**
	 * @return string
	 */
	function say_hello()
	{
		return 'hi!';
	}

Replace the contents of your `~/demo/0.1.x/themes/classic/landing.php` file
with `<?= $context->say_hello() ?>`.

If you open the site you should now see "hi!"

It's good to use the correct variable when accessing method even though they
are on the same class since they might not always be on the same class;
`say_hello` is not metadata so we use the `$context` variable.

Continuing on with the discussion on themes, themes may have a variety of
different modules: style module, scripts module, etc. You can define your own
if you want; if you don't like how the style module supports multiple styles
and requires sass you can just make your own custom style module that just
works with vanilla css.

Which modules are enabled for the theme is determined by the loaders section in
the theme `+theme.php` configuration file, but we won't cover that here.

All modules in general will work with the same target you provide to the theme.
Where the theme resolves it to a page composition, the module resolves it to
it's own composition. In the case of scripts for example the target specifies
which scripts appear on the page (assuming we don't specify we want all scripts
on all pages).

We will add basic script to the page to illustrate.

We start by executing a monitoring script that will compile the javascript to
single file. Please run: `~/demo/0.1.x/themes/classic/+scripts/+start.rb` this
will open a console; while the console is open files in the scripts directory
will be monitored for changes and compilation done automatically.

Now open the main configuration file `+scripts.php` in the same folder as the
monitoring script. As with the theme configuration we specify the rules for
the target in the `mapping` section; or to be exact `targetted-mapping` since
we want per page customization. Currently there should be a rule there
`frontend` already defined, since we're not using it, please rename `frontend`
to `landing` and add `hello` to the existing list. Your `mapping` section should
look like this when done:

	'targeted-mapping' => array
		(
			'landing' => array
				(
					'base',
					'hello'
				),
		),

Now we need to create the file `hello`. If you look at the configuration you'll
notice that the `sources` is set to `src` so our `hello.js` file would be
located at `~/demo/0.1.x/themes/classic/+scripts/src/hello.js`.

In the file just add an `alert('hello, world');`. If you now open the site you
should see an alert with "hello, world." You may have to Ctrl+R, alternatively
you can simply update the script version in the configuration file with the
mapping. If your browser has support for source maps you should be able to find
`src/hello.js` in your inspect menus.
