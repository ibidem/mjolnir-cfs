DISCLAIMER: *The following documentation is work in progress and has yet to be
properly formatted, proof read, or completed. It is provided as-is even in it's
current state so that it may be of use, as well as for internal development
purposes.*

Mjölnir, pronounced "mee-uhl-neer", is an all purpose PHP module-based
framework (and toolkit) primarily aimed for web development but adept at
any task otherwise possible though PHP. Based on a (PSR-0 compatible) cascading
modular class and file system, PHP traits and convention though interfaces, the
framework is designed to mold itself to your use case. The main design goals
are, in order:

 * maintainable code
 * flexible infrastructure
 * reusability
 * security
 * ease of use & simplicity
 * easy integration with other tools

In Mjölnir all classes, methods, variables, and values are replaceable,
overwritable, customizable, extendable, and discardable. If it exists, it exists
to be given a purpose, not as a requirement. Files, user interaction, execution,
request patterns, project structure, are all designed to allow for
interpretation of the problem at hand.

The framework is based on PHP, because PHP facilitates the frameworks technical
requirements, via things such as class autoloading, among other features.

The framework is designed around up-to-date PHP. At this time PHP 5.4.4 and
above is required; using the latest version is highly recommended, and by the
frameworks philosophy new (notable and useful) features in the language will be
adopted as soon as possible.

For understanding how the framework works, and how to use it effectively, it is
recommended to continue reading the documentation, starting with the
cascading file system module. Other modules are all contextual in nature,
so after understanding how the module system works feel free to skip to any
point of interest to your own projects.

All documentation is created to be human readable; and is part of the codebase,
and the release process. As per the release philosophy a version can not be
stable with out complete documentation. There are no API docs, since it's
usefulness is debatable; to conserve time it is ignored. All code has been
written to be readable by itself, various docblock patterns have still been
used to the extend that is useful for editor autocompletion and other tooling.
Even though doc-style comments are almost intentionally omitted the code is
still commented extensively; in place of machine language paragraphs and
examples are written in plain english and detail.