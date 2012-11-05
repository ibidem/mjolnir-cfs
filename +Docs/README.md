Cascading File System
=====================

2012 Edition, by Ibidem Team

**Table of contents**

1. Modules
2. Loading Classes
3. Loading Files
4. Loading Configuration Files
5. Composer Integration
6. Overwriting Classes
7. Overwriting Behavior in a Classes
8. Overwriting Configuration Entries
9. Overwriting Files

-

The Cascading File System (cfs) module allows the implementation of projects
based on a modular pattern where points of interest in an application are split
into modules (separate folders with a namespace) and stacked, with top modules
taking precedence over lower modules. The process is similar to autoloading in
the Kohana framework but modules follow a PSR-0 compliant structure and fully
supports namespaces.

If properly applied, all classes, files, and configurations on the application
become easily overwritable and customizable.

The principle works with dependency injection but will for most cases solve the
same problems with less hassle, less code and more intuitive patterns (eg. plain
old `extend`). It's important to note that this pattern will also make good
use of language features (such as autoloading), while dependency injection
implementations in PHP tend to be complicated and wanting due to missing
language features to facilitate them (ie. generics). Dependency injection also
works on the promise of being useful later, while the module pattern has many
useful traits just by being there (easier to read code structure,
easier to integrate code, re-usability, etc).

This module is part of Mjolnir, but may be used on it's own for creating
projects, frameworks, etc. Its only foreign dependency is to the `mjolnir\types`
namespace (a pure interface module), where it retrieves type information for
caching and database binding methods (both related to optional features).
`mjolnir\testing` is also used by this module, but only for behaviour tests; we
recommend running tests in mjolnir setup so this should not be a concern for
anyone who wishes to use only this module.

For creating an application based on this module, but not on mjolnir as a whole
the [mjolnir-template-app](https://github.com/ibidem/mjolnir-template-app) can
still be used as guide. Note that aside from `DOCROOT/mjolnir.php` all the
structure is merely a recommendation.

If you wish to create your own version based on this module but keep most of it
you can include this module as a dependency to your project and create another
class which extends this one; you can rely on composer to do the loading of
this class and related dependencies, just extend `\mjolnir\cfs\CFS` in your
new class.

For versioning information, see:
https://github.com/ibidem/ibidem/blob/master/versioning.md
