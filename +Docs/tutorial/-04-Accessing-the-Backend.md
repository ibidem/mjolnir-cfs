On unix systems by default you have to prefix executable files with `./` due
to `$PATH` ordering (local directory `.` is last, instead of first). In
development you can change this for convenience or just remember you have to
prefix with `./` (ie. `./order`) all the commands bellow.

	cd ~/demo/0.1.x/
	order help

This is the list of all commands (aka. tasks), please see on screen help for
more information. You can skip the help argument; useful for quick reference.
What commands you see depends on your modules (you can create your own tasks).

	order compile

Try opening the site. You should see a 500 error. If you enable development in
`~/www/config.php` the system won't hide the error. We are going to continue on
assuming development is disabled.

	order log:short

This log maintains a 1 line entry for all errors. While the command is open new
errors will pop on screen. You should see the error "Theme Corruption: No
themes present in environment file." You can exit with `Ctrl+C`.

	cp -R themes/empty-theme themes/classic

Do **not** use the `mv` command to do this or `git mv`, `empty-theme` needs to
remain as-is for pulling updates. You don't have to call it "classic," just
remember that's how we're referring to it as.

Open `~/demo/0.1.x/etc/environment.php` and update the `themes` section with
`'classic' => $syspath.'themes/classic/',`. The section should look something
like this:

	'themes' => array
		(
			'classic' => $syspath.'themes/classic/',
		),

If you open the site now, you should... still see an error.

If you're observant you'll notice you've been redirected to
`127.0.0.1/demo/access/signin`, this is because you have no routes,
controllers, etc. What you are viewing is the internal default access page
which you were redirected to due to not having access to view anything else.
The reason it's not working is because we didn't set up the database, yet. If
take a moment to run `order log:short` you should see confirmation of this.

So let's set it up.

We're going to assume you know how to create your database in phpmyadmin, the
mysql console or whatever your favorite tool is for doing so. We do not provide
automated database creation since that generally involves potentially insecure
database configurations. We recommend creating a user specifically for your
application and having said user have access only to your
applications database.

If you're creating the database and/or user at this time, please review the
database settings in the configuration
`~/demo/private/config/mjolnir/database.php`.

	order pdx:reset

This will create the database tables and update the schema to the latest
version. To see what it did you can view the migration history with
`order pdx:history --detailed`.

If you open the site now you should see a form; however by default no
administrator accounts are created.

	order make:user -u admin --role admin -p adminadmin --email admin@example.tld

This will create a `admin` user called `admin` with the password `adminadmin`.
The `-u` flag stands for `--username` and the `-p` flag stands for `--password`.

You can now go back to the form and log in with the new user `admin`. After
doing so please proceed to the "Backend" section (see link on screen).

The backend is the main administration panel; it is generally meant for with
server knowhow so applications may have their own custom "admin panels" which
their non-technical "administrators" may frequent.

The panels in the main administration panel are customizable; you can easily
add more stuff and modules can easily add panels.

At the moment you should be viewing the "System Information" panel which shows
the current state of the system by running the `mjolnir/require` configuration
in every module. Your current state should be yellow and marked as "Usable." In
production you want it to be all green. At the moment the only errors you
should be seeing should be "non-dev email driver" and "system email," so we'll
skip over fixing them.

Please proceed to "File Permissions." It is mandatory at this point the section
be "Stable." On windows you should have an easy time, but on unix system you
may have trouble. The point of the section is to check that all file
permissions are in order. Every time you pull new changes or update vendors or
do something else (eg. compile files) check back with this section, it will
ensure no server specific bugs emerged; it's also good idea to check the
"System Information" just in case some faulty configuration was pulled in.

Once file permissions are in the green you're good to go. You can check out the
"Users" section there but you should see only an admin user at this time.