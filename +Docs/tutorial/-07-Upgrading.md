As the previous sections this is more server oriented for clarity.

It's assumed we're upgrading the instance created in the previous part.

	cd ~/demo/
	cp -R 0.1.x/ 0.2.x/

We `cp` (ie. "copy") instead of `git clone` to preserve any file permissions,
special files like `.www.path`, logs, etc. It's also much faster in some cases
since we don't have to connect to the internet to check for updates and such.

Update `~/www/config.php` paths, namely "sys.path", enable maintenance mode.
The good thing about keeping our private keys and such in a seperate "private"
folder is that we now don't have to worry about it. Since we `cp`'ed the
directory, if we don't do any database upgrade we can change back to the old
source tree by reverting `sys.path` to the `0.1.x` version.

	git pull origin production
	bin/vendor/install
	order compile

If you are using packaged mode you can skip the `compile` step.

	order pdx:upgrade --dry-run

Check that everything is as expected. Then run it:

	order pdx:upgrade

At this point check your admin panel that everything is in the green.

When everything is in order disable maintenance mode in `~/www/config.php`, and
you're done.

Keep in mind projects may have extra dependencies that require extra
configuration to be performed.
