
	bin/vendor/install

You can also use `bin/vendor/development`, here's the difference:

* development uses `~/demo/0.1.x/etc/composer.json` and git clones the repositories
* development usually contains things like testing dependencies
* install uses `~/demo/0.1.x/composer.json` and tries to use prepackaged archives
* install is super fast compared to development
* install skips non-production dependencies (note that dependencies have tons
  of dependencies of their own so its a LOT of stuff)

In production you almost always want `bin/vendor/install`.

You can check the dependencies installed with `bin/vendor/status`.

You can also edit the composer file(s) and run it again to install more.