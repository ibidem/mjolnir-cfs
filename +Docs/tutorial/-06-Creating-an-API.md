This section follows immediately after the code you obtain from the previous
sections and focuses on creating a REST API.

For this section it's highly recommended you install the Postman REST client
extention. You may install another so long as you can follow along. The
extention is not mandatory for completing this section but using it (or a
similar technique) is highly recommended.

The reason you need the extention is for testing; it's quite pointless to test
your api while running the application. Worry about your api working with an
extention like Postman that lets you run POST, GET, DELETE, PUT, etc requests
with json payloads against your server and once you know it's all working worry
about the frontend application you create for your users consuming it; who you
create first, the backbone collection or the api is up to your personal
preference, just remember that you should not work on both at the same time
since, while by no means impossible, it's harder and more time consuming even
for small issues.

There are other reason too, such as redirects; if you test your api in the
browser the internals will try to redirect you to an appropriate error page in
case of an error; while helpful in a live application it's very disruptive in
development (you can also enable development mode to get rid of this
functionality, go to your `~/www/demo/config.php`).

At this point you may choose to replace your `landing.php` with the following
to help you build a Backbone application.

	<?
		namespace app;

		/* @var $theme ThemeView */

		$templates = array
			(
			// pages
	//			'Dashboard' => 'pages/Dashboard',
			// modules

				// no module templates

			// extentions

				// no 3rd party extentions

			);
	?>

	<div id="sheep-context">
		<div class="container">
			<h1>Loading...</h1>
		</div>
	</div>

	<? foreach ($templates as $template => $path): ?>
		<script type="text/x-underscore-template" id="<?= $template ?>-template">
			<?= $theme->partial("templates/$path")->render() ?>
		</script>
	<? endforeach; ?>

The new `landing.php` will solve most of your issues with templates. You just
put all your templates in a template folder and your application will dump them
(based on the template configuration you specify above) into the `landing.php`
page which in turn is your main application entry point. This is why we changed
the name to "landing" in the early steps; home and frontend are generally more
appropriate for static pages.

We're not going to make use of it in this section since we'll be focusing on
creating the api, that should be roughly all you need to be able to start using
almost any backbone beginner tutorial.

Getting back to the API itself, please replace your
`~/demo/0.1.x/etc/config/routes.php` with the following:

	<?php

		$id_regex = '[0-9]+';
		$id = ['id' => $id_regex];

		$apimethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

	return array
		(

		// ---- API ---------------------------------------------------------------

			// clients

			'/api/v1/client(/<id>)'
				=> [ 'v1-client.api', $id, $apimethods],

			'/api/v1/clients'
				=> [ 'v1-clients.api', [], $apimethods],

		// ---- Pages -------------------------------------------------------------

			'/'
				=> [ 'landing.public' ],

		);

There's not too much of a difference, we just added 2 api routes. The 2nd and
3rd parameters are optional and when specified must be arrays, the first one
specifies what regex the segments need to match to be valid (by default it's
`[]` as in no parameters which defaults the parameters to matching everything)
and the 3rd one specifies what methods are allowed (by default `['GET', 'POST']`
if not specified).

We use `$id` as an array since PHP allows `+` on arrays and it acts as a merge.
So if we had `$name = ['name' => '[a-z]+']` then `$id + $name` would be
equivalent to `['id' => '[0-9]+'] + ['name' => '[a-z]+']` and produce
`['id' => '[0-9]+', 'name' => '[a-z]+']`. This is merely used for clarity,
`$id + $name` is very short and to the point.

Our two new routes use the `api` stack. By default the framework does not
come with an `api` stack to avoid modules polluting logic by making
assumptions on how the `api` stack works. For that reason the `api` stack is
considered application reserved and you have to define it yourself. To do so
create the file `~/demo/0.1.x/modules/core/+App/config/layer-stacks.php` with
the following code:

	<?php return array
		(
			'api' => function ($relay, $target)
				{
					$json = \app\CFS::config('mjolnir/layer-stacks')['json'];
					return $json($relay, $target);
				},

		); # config

You now have defined the stack. We cheated and just have it call the `json`
stack, but that's perfectly fine implementation for our use case.

In Postman access `127.0.0.1/demo/api/v1/clients`. At this point you should see
an error `{ "error": "URL called is not a recognized API." }` with the 404
status. This is because we haven't given access rights to our api.

To give access rights, in `~/demo/0.1.x/etc/config/mjolnir/access.php` replace
your previous `Auth::Guest` rule with the following version:

	Auth::Guest => array
		(
			Allow::relays
				(
					'landing.public'
				)
				->unrestricted(),

		// API, v1

			Allow::relays
				(
					'v1-client.api',
					'v1-clients.api'
				)
				->unrestricted(),
		),

We'll also need to create the appropriate classes to handle the request.

	order make:module --name api.v1 -n 'demo\api\v1'

Don't forget to enable it in your `~/demo/0.1.x/etc/environment.php`.

	order make:class -c '\demo\api\v1\Controller_V1Clients'

Open the new class, located in
`~/demo/0.1.x/modules/api.v1/Controller/V1Clients.php` and change the extended
class to `\app\Controller_Base_V1Api`.

In Postman now access `127.0.0.1/demo/api/v1/clients`, you should see the error
"Not Implemented" with a 501 status. The error is generated by the placeholder
GET handler provided by `Controller_Base_V1Api`.

Now that we got the groundwork done for `127.0.0.1/demo/api/v1/clients` it's
time to get the internals sorted. There are several ways to do this, one way is
to use `Marionette` models which are models specifically designed to help in
creating APIs. More precisely they follow Backbone's flavor of APIs and to help
managing them they also mirror backbone's conventions, so you have a class for
the collection and a class for the model and all methods you call on are
equivalent to the http methods you would call. So calling `$model->put($conf)`
will update the entry and return an updated entry, calling
`$model->patch($conf)` will partially update the entry and return the updated
entry, calling `$collection->get($conf)` will return all the entries, and
so on.

The other type available by default (you're free to define your own system that
works for you) is a static library model, where we simply define a class with
static methods and use traits to inherit functionality; this is very flexible
but less automated—in practice this means we can get complicated jobs done
easier and more intuitively when using the static library model method (because
you have a lot of control) but can get a lot of simple jobs done quicker when
using the `Marionette` method (because you have a lot of automation). We'll
talk about the static library models later, for clients we'll show how to
define a `Marionette` model system.

We are going to continue with Marionette models. We're going to assume a client
is defined by merely a "family_name" and "given_name".

	order make:class -c 'demo\core\ClientCollection'
	order make:class -c 'demo\core\ClientModel'

In `~/demo/0.1.x/models/core/ClientCollection.php` change the extended class to
`\app\MarionetteCollection`. You can remove the `@todo`, the class will work
as-is based on it's name only.

In `~/demo/0.1.x/models/core/ClientModel.php` change the extended class to
`\app\MarionetteModel`.

With the main classes created we now need to create the configuration file that
goes with them. Create the file
`~/demo/0.1.x/modules/core/+App/config/client.php` with the following content:

	<?php return array
		(
			'name' => 'client',

			'key' => 'id',

			'fields' => array
				(
					'id' => 'number',
					'given_name' => 'string',
					'family_name' => 'string',
				),

		); # config

Finally we need to a database table. For this we'll create a paradox migration.
Paradox migrations work roughly like this:

 1. you have channels, usually each module has it's own channel but sometimes a
    set of modules may have their own; for our application we'll create a
    "demo" channel

 2. each channel has it's own version history, so 1.0.0 for the demo channel is
    different then 1.0.0 of the mjolnir-access channel

 3. the system keeps the history in the database; you can view it on the
    command line by using pdx:history

 4. the system also works by default in lock mode so it won't allow destructive
    operations such as uninstalling the database

 5. each migration is a entry in the mjolnir/paradox configuration file, which
    in our case would be located in
    `~/demo/0.1.x/modules/demo/core/+App/config/mjolnir/paradox.php`

 6. when you run pdx:upgrade the system will look for changes and run any
    previously not executed migrations

 7. migrations only go forward so when testing and or moving between branches
    with different database structures in development you'll need to generally
    turn of database locking so you can perform database resets
    (ie. uninstall -> reset to latest, or specific version + upgrade if testing
    migrations)

 8. migration operations NEVER call on anything but basic low level database
    operations with the only exception being the "table" static method in
    modules which provides the table name. The reason for this is that anything
    above low level apis is dependent on the state of the database and hence
    dependent on both certain things existing and certain things existing in a
    particular state, both of which are not guaranteed when the migrations are
    running. Even a basic count method can potentially reference a certain
    security field when performing the count which might only be available from
    a certain migration onward.

So lets start by creating the paradox file, for clarity we'll be relocating the
actual migration part to a separate configuration file (this is recommended
since migrations are many and can get quite long).

Create the file `~/demo/0.1.x/modules/demo/core/+App/config/mjolnir/paradox.php`
with the following contents:

	<?php return array
		(
			'demo' => array
				(
					'database' => 'default',

					// versions
					'1.0.0' => \app\Pdx::gate('demo/1.0.0'),
				),

		); # config

`\app\Pdx` is the main "Paradox" library class, provides access to helpers (and
the operations themselves if you need to call them in code). The call to
`\app\Pdx::gate('demo/1.0.0')` will basically add `timeline/` to the key and
load it as a configuration file, returning it as an array.

Create the file
`~/demo/0.1.x/modules/core/+App/config/mjolnir/timeline/demo/1.0.0.php` with
the following contents:

	<?php return array
		(
			'description'
				=> 'Install for Clients.',

			'configure' => array
				(
					'tables' => array
						(
							\app\ClientModel::table(),
						),
				),

			'tables' => array
				(
					\app\ClientModel::table() =>
						'
							id          :key_primary,
							given_name  :name,
							family_name :name,

							PRIMARY KEY(id)
						',
				),

		); # config

The description will be dumped into the history when the migration runs. The
`configure` key is for providing meta information to the migration system, in
this case we're telling it what tables it should be aware of with this
migration (this informations is used for things like uninstalling). The other
`tables` key is telling it which tables we want to create; we're using
placeholders for easy customization of the installation (they're also easier
to read).

With a lot of configuration options you can just throw in a function and do
whatever you want, but this requires some knowledge on the internals so we'll
leave it as-is.

In this example we also just used one channel, but you can define as many
channels as you need at the application level. You can also set dependencies
between versions. Version `1.1.0` of `demo` might depend on version `1.0.1` of
`mjolnir-access`. This is done by defining a `require` key with an `array` of
dependencies. Generally it's recommended you place the array dependencies in
the paradox file which when using `Pdx::gate` you would do by passing the array
as the second argument.

With our migration in place we now only need to upgrade our database:

	order cleanup
	order pdx:upgrade --dry-run

We use `cleanup` in case the system cached the previous configuration state.
The `pdx:upgrade --dry-run` will show you the steps it will do but not actually
do them. You should see 1 line that reads "1.0.0 demo" as in
"run the demo 1.0.0 migration."

	order pdx:upgrade

You should get "Upgrade complete."

	order pdx:history

You should see your migration as the last one executed. You can use
`--detailed` to get the description too.

Now that we have the database and model we can return to our api.

Add the following method to
`~/demo/0.1.x/modules/api.v1/Controller/V1Clients.php`

	/**
	 * @return array
	 */
	function get($req)
	{
		$collection = \app\ClientCollection::instance();

		$conf = [];
		! isset($req['limit']) or $conf['limit'] = $req['limit'];
		! isset($req['offset']) or $conf['offset'] = $req['offset'];

		return $collection->get($conf);
	}

In Postman access `127.0.0.1/demo/api/v1/clients`, you should see "200 OK" on
the status and `[]` on the returned value.

With the Collection part done, we'll now create the Model part to get some
items in. We've already got the database and model class setup from before so
we just need to create the api.

	order make:class -c '\demo\api\v1\Controller_V1Client'

The file created is located in
`~/demo/0.1.x/modules/api.v1/Controller/V1Client.php`. Please change the
extended class to `\app\Controller_Base_V1Api` and add the following methods:

	/**
	 * @return array
	 */
	function get($req)
	{
		$id = $this->channel()->get('relaynode')->get('id');
		$model = \app\ClientModel::instance();

		$entry = $model->get($id);

		if ($entry == null)
		{
			$this->channel()->set('http:status', '404 Not Found');
			return [ 'error' => 'Client with id ['.$id.'] does not exist.' ];
		}

		return $entry;
	}

	/**
	 * @return array
	 */
	function post($req)
	{
		$collection = \app\ClientCollection::instance();
		$entry = $collection->post($req);

		return $entry;
	}

	/**
	 * ...
	 */
	function delete($req)
	{
		$id = $this->channel()->get('relaynode')->get('id');
		$model = \app\ClientModel::instance();
		$model->delete($id);

		return null;
	}

	/**
	 * @return array
	 */
	function patch($req)
	{
		$id = $this->channel()->get('relaynode')->get('id');
		$model = \app\ClientModel::instance();

		return $model->patch($id, $req);
	}

A few things to explain before moving on. The `$this->channel()` call refers to
the channel for communication used by the current request. The channel object
is shared between layers, the controller and whatever else participates in the
request, and its purpose is to allow for isolation of metadata specific to the
request. In this case we're using it to get the `relaynode` (ie. the route
object, since routes are relays) and from the relay node we retrieve the id
parameter in our route. In the get method we're also communicating with the
channel how the `http:status` should change to `404 Not Found` for the case
where the entry does not exist.

You might be confused by why we're using `ClientCollection` to perform the
post operation. This is a slight deviation to the way Backbone works for
correctness; the correct way to create a new entry in a model is to post to
the collection, but backbone posts to the root of the model, hence why we're
calling the collection there (the correct way to do it) in the client api (the
api backbone expects to be able to post new entries to). Just to be clear, the
very obscure functionality related to doing a POST against a model is not
supported.

If you wish you may also change the routes like so:

	'/api/v1/clients/<id>'
		=> [ 'v1-client.api', $id, $apimethods],

	'/api/v1/clients'
		=> [ 'v1-clients.api', [], $apimethods],

ie. add an "s" to the v1-client.api route and make the id mandatory.

Now you can have the post method into the `Controller_V1Clients` class instead
of `Controller_V1Client`.

It is indeed more correct; we've chosen to explain it as we did in case you
wanted to have separate urls, and because it's less confusing on how the url
work with respect to what the models in backbone are calling. With the above
code what will happen is backbone will try to call the root of the client url
and the request won't match but will match the collection url since it's
equivalent to the root.

In Postman, set the method to POST, select payload as raw (JSON) and run the
following:

	{
		"family_name": "Joe",
		"given_name": "Average"
	}

You should get back a json with the fields and an id. Run it 4 more times; feel
free to change the name if you wish, but since we didn't add in validation you
can run it as-is.

Here are some basic operations:

 1. in Postman, do a GET request on `127.0.0.1/demo/api/v1/client/2`, you
    should get the entry with id 2.

 2. in Postman, change the GET to a DELETE and run the request

 3. in Postman, change back to GET and run the request, you should get a 404
    status this time with an error message

 4. in Postman, do a GET on `127.0.0.1/demo/api/v1/clients`, if you been
    following along you should get entries with IDs 1, 3, 4, 5 since we DELETEd
	entry with ID 2 earlier.

 5. in Postman, enable URL params and add "limit" with the value "2", you
    should now only see entries 1 and 3

 6. in Postman, add the url parameter "offset" with value "1", you should now
    see entries 3 and 4

We won't cover how to actually write the backbone code since at this point
there's no different between writing it in your application or a plain html
file (we showed how to setup javascript earlier; which is the main component
you need).

Finally we're also going to show how to use a static model library. Generally
when you have an application that only uses static model libraries to function
you would go with the naming convention `Model_Client` which will place your
class inside a `Model` directory. When working with `Marionette` classes the
naming convention is `ClientLib` since that places it next to the `Model` and
`Collection` class, which is a lot easier to manage. Obviously we're going to
go with the `ClientLib` variant.

	order make:class -c '\demo\core\ClientLib'

Remove the extends declaration and add the following traits to the class body:

	use \app\Trait_Model_Factory;
	use \app\Trait_Model_Utilities;
	use \app\Trait_Model_Collection;

We'll also need to resolve the table name. Normally we would add a static field
`$table` with the name but since we are using marionettes and have a
configuration file setup we'll overwrite the table() method to retrieve the
correct value so everything is in one place.

Now add the following method so the the model knows which table to use:

	/**
	 * @return string table name
	 */
	static function table()
	{
		return \app\ClientModel::table();
	}

To show that it all works we'll modify the get method for the collection, since
other methods work differently then what backbone expects.

Replace the method "get" in `Controller_V1Clients` with the following version:

	/**
	 * @return array
	 */
	function get($req)
	{
		$limit = isset($req['limit']) ? $req['limit'] : null;
		$offset = isset($req['offset']) ? $req['offset'] : 0;

		return \app\ClientLib::entries(1, $limit, $offset);
	}

In Postman, do a GET on `127.0.0.1/demo/api/v1/clients` with limit 2 and
offset 1. You should get 3 and 4 like before.

We won't go into exact examples on why you would use one or the other but to
give you an idea, lets say you needed to have a very special relationship and a
very specific data type. In the marionette model you have to (a) find a way to
interpret it though GET, POST, PUT etc, (b) write a driver to handle all the
operations in a dynamic way (which isn't as easy as it sounds), and (c) use the
driver in your configuration. On the other hand in the static library method
you just boil down the problem to raw SQL and place it in whatever method you
want; you can just create your own method, since the model is designed to act
as a library, not follow an interface, and all methods are independent (a given
since they are static). Writing raw SQL solves problems really really fast. So,
as mentioned earlier, the question boilds down to: do you want control, or do
you want automation. Mind you both have mechanisms for dealing with repetition,
drivers for the marionette system and native traits for the static library
model system.

Also, if you ever need a special static method to perform an operation, the
correct way is to create the `ClientLib` class equivalent, since the `Model`
and `Collection` classes are specifically designed to just consume drivers and
should not be forced to do anything more. There are other reasons too, the Lib
class has access to static helpers, the Lib class works with static methods,
whereas the others require instantiation and so you may require instantiation
of the class you're into to call methods you need, the Lib class is also a
clearer place to have the methods then having them split over two classes, etc.

But again, as mentioned earlier, you can simply create your own model system to
suit your own needs, these are just the defaults provided.
