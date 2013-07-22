	cd ~/demo/0.1.x/
	cp -R drafts/keys.draft/ ../private/

You should now fill in configuration information. Since we're just starting
it's only library specific configuration we have to deal with so go into
`~/demo/private/config/mjolnir` and review the configuration files there, they
should be fairly self explanatory.

Here are some keys to help you fill them faster, and also to give you an idea
of how they should look; **only use these in development**.

* recapcha testing public key: 6Lfy4d4SAAAAADCqgQpTXxyHVEOFc-ViJP334ZqY
* recapcha testing private key: 6Lfy4d4SAAAAAJXHfni6PmLpOAVFB80B-0eHlGJf
* example cookie key: U0YgC213 ...200 characters... b0XLuyqvxN
* example api key: JKUCI0 ...200 characters...  Bn4YsaAO2

That covers the private files.

We now need to copy the public files. For this example we're going to assume
we're installing into a folder on our domain, called "demo."

	cd ~/demo/0.1.x/
	cp -R drafts/www/ ~/www/demo/

We now need to also copy any public server specific files, in our case since
we're using apache we'll need the contents of `www.apache`; unfortunately there
is no easy command for this, you'll just need to do it mostly manually.

We'll also need to configure the files in question, in our case of using apache
`.htaccess` files we just need to set the `RewriteBase` in the
`~/www/demo/.htaccess` file to `/demo/` since we're in a folder (had we been on
the root of the site, we wouldn't have had any configuration to do).

All that's left now is to configure the main site settings, located in the
`~/www/demo/config.php` in our case.

Note that the file in question is split into (from the top)
"Important Settings," "Performance Settings" and "Optional Settings." As you
might guess you only need to fill in the "Important Settings" to get up and
running.

Here is an extract of said settings:

	# Important Settings
	# ---------------------------------------------------------------------

	// where are your passwords and secret keys located?
	'key.path' => null, # absolute path
	// where are the project files located?
	'sys.path' => null, # absolute path

	// are you in a development environment?
	'development' => false,

	// what is the domain of your site? eg. www.example.com, example.com
	'domain' => 'your.domain.tld',
	// is your site in a directory on the server?
	'path' => '/', # must end and start with a /

Simply follow the comments. Here's how it would look like in our case:

	# Important Settings
	# ---------------------------------------------------------------------

	// where are your passwords and secret keys located?
	'key.path' => '/home/site_user/demo/private/', # absolute path
	// where are the project files located?
	'sys.path' => '/home/site_user/demo/0.1.x/', # absolute path

	// are you in a development environment?
	'development' => false,

	// what is the domain of your site? eg. www.example.com, example.com
	'domain' => '127.0.0.1',
	// is your site in a directory on the server?
	'path' => '/demo/', # must end and start with a /

We will also need to tell our project of the private files.

	cd ~/demo/0.1.x/
	echo '/home/site_user/www/demo/' > .www.path

You can also add a `.key.path` file but the system will read the path from
the `/home/site_user/www/config.php` if you have a `.www.path`.

At this point you have a very good base. However the `mj/2.x/blank` is a very
minimal branch designed to allow you to easily pull in changes with out
manually having to change the files yourself (ie. updated drafts and so on).
This means it has no controllers, no themes, etc.