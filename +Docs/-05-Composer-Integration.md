First of all you may load and use any composer compatible projects. It is not
recommended to configure them as modules; they should be used via the namespace
resolution only (so that they are handled by composer only).

Modules, as previously described, are PSR-0 *compatible*, so as long as there
are no major dependencies to the cascading file system, they may be loaded
directly via composer and used like a regular composer package.

If a composer based class would serve better as a class within the cascading
file system, the recommended way of integrating it, assuming it was not
designed to be used in this context to begin with, is to construct a wrapper and
extend it. *This is also the case for any embeded code within the module.*

Ideally modules will define any dependencies via their `composer.json` file,
which assuming the module is itself loaded via composer results in said
dependencies being transparently pulled in and updated as is the case.
