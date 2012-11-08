DISCLAIMER: *The following documentation is work in progress and has yet to be
properly formatted, proof read, or completed. It is provided as-is even in it's
current state so that it may be of use, as well as for internal development
purposes.*

Mjölnir, pronounced "mee-uhl-neer", is an all purpose PHP module-based
framework (and toolkit) primarily aimed for web development but which can handle
any task otherwise possible though PHP. Based on a (PSR-0 compatible) cascading
modular class and file system, PHP traits and convention though interfaces, the
framework is designed to mold itself to your use case, rather then force any
convention on you. The main design goals are, in order:

 * maintainable code
 * flexible infrastructure
 * reusability
 * security
 * ease of use & simplicity
 * easy integration with other tools

In Mjölnir all classes, methods, variables, and values are replaceable,
overwritable, customizable, extendable, and discardable. If it exists, it exists
to be given a purpose, by you. Files, user interaction, execution, request
patterns, project structure, are all designed to allow or work via your
interpretation of the problem at hand. Because let's face it, nothing is
perfect forever, and one size most definitely never fits all.

The framework is based on PHP, and not some other language, for the simple
reason that PHP facilitates the best the frameworks technical specification
(though class autoloading; among other notable features such as accessibility).

That being said, the framework is designed around up-to-date PHP. At this time
PHP 5.4.4 and above is required; using the latest version is highly recommended,
and by the frameworks philosophy new (useful) features in the language will be
adopted as soon as possible.

For understanding how the framework works, and how to use it effectively, it is
recommended to continue reading the documentation, starting with the
cascading file system module. Other modules are all contextual in nature,
so after reading how to use the cascading file system module (and possibly the
base module) feel free to skip to any point of interest to your own projects.

All documentation is created to be human readable; and is part of the codebase,
and the release process. As per the release philosophy a version can not be
stable with out complete documentation. There are no API docs, since it's
usefulness is debatable. All code has been written to be readable by itself,
various docblock patterns have still been used to the extend that is useful for
editor autocomple and other tooling. Even though doc-style comments are almost
intentionally omitted the code is still commented extensively; in place of
`@param` and other machine language paragraphs and examples are written in
plain english and detail.
