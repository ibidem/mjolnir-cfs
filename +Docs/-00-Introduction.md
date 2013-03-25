The Cascading File System (cfs for short) module allows the implementation of
projects based on a modular pattern where points of interest in an application
are split into modules (separate directories with a namespace) and stacked in
order of priority, with top modules taking precedence over lower modules. The
modules are built on a PSR-0 compliant structure and fully support namespaces.

If properly applied, all classes, files, and configurations on the application
become easily overwritable and customizable.

The system is compatible with dependency injection but for most cases dependency
injection overlaps with the module system in purpose. Modules will solve roughly
the same problems with less hassle, less code, and more intuitive patterns.

This module is part of Mjölnir, but may be used on it's own for creating
projects, frameworks, etc. Its only foreign dependency is to the `mjolnir\types`
namespace (a pure interface module), where it retrieves type information for
caching and database binding methods (both functionally optional features).
`mjolnir\testing` is also used by this module, but only for behavior tests; we
recommend running tests in a typical mjolnir setup so this should not be a
concern for anyone who wishes to use only this module.

For creating an application based on this module, but not on mjolnir as a whole
the [mjolnir-template-app](https://github.com/ibidem/mjolnir-template-app) can
still be used as a guide. Note that all the structure is merely a
recommendation.

If you wish to create your own version based on this module but keep most of it
you can include this module as a dependency to your project and create another
class which extends it via composer. You will have to extent `\mjolnir\cfs\CFS`
in your new class.

<hr/>

For versioning information and methodology see
<https://github.com/ibidem/ibidem/blob/master/versioning.md>
