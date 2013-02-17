For a class (from a registered module) to be loadable the following conditions
must be met.

 1. The module in which the class is present must be known by the autoloader;
 meaning, when using the recommended structure you must specify it in your
 `environment.php` file. If you are relying on a non-default structure this
 condition resumes to: it must be included by `CFS::modules`,
 `CFS::frontmodules`, `CFS::backmodules`, or for namespace only access
 `CFS::namespacepaths`.
 2. If underscores within the class name are replaced with directory separators
 specific to the system, the class should result in a valid path segment and in
 combination with the path to the module itself and the current extention
 (defined by the current value of EXT) should produce a valid path to the class
 file. Confused? Let's say we have the example class
 `Controller_AcmeOrganization` the correct path to it if `MODULE` is the path
 to the module, and `EXT` is `.php` is `MODULE/Controller/AcmeOrganization.php`.
 If the class is placed in any other file it will not be recognized.
 3. The full namespace of the class should correspond (exactly) to the namespace
 defined for the module. So as an example, the `\mjolnir\access\ReCaptcha` class
 resides in the `access` module, which has the namespace `mjolnir\access`.
 4. Another file with the same path segment pattern (ie. same class name) is not
 available in a higher module (this DOES NOT apply to namespace invocation;
 discussed bellow)

If all conditions are met the class will be loaded. Otherwise it will be passed
on to any other autoloader on the system (eg. bridges to other module systems,
composer's autoloader, etc).

Let's take an example,

    <?php namespace acme\security\access;

    # MODULE/Controller/AcmeOrganization.php

    class Controller_AcmeOrgnaization
    {
        // ...

    } # class

We can call this class in a number of ways. First we can call it by namespace:

	\acme\security\access\Controller_AcmeOrgnaization

If all else fails this method will always work assuming you have composer setup
correctly, since all modules are PSR-0 compliant.

If we say don't care what `Controller_AcmeOrganization` it is in
`acme\security` we can simply call it by:

	\acme\security\Controller_AcmeOrganization

Similarly, if we don't care for the security segment, we can call:

	\acme\Controller_AcmeOrganization


There are however three conditions to this shorthand namespace resolution:

 1. the full namespace must be a namespace known to the cascading file system;
 namespaces only known via composer will not resolve.
 2. you may only omit entire segments at a time; so
 `\acme\sec\Controller_AcmeOrgnaization` (note: "sec" instead of "security")
 will not resolve to our example class.
 3. the namespace you are using as a shorthand must not be registered in the
 cascading file system. This is purely by design to prevent false positives. If
 the namespace is registered and the class is not within it then the class will
 NOT resolve. This behaviour is also intended to avoid confusion.

When extending any class in mjolnir it is recommended (and expected) you use the
shorthand `mjolnir` namespace; so if we had a class `mjolnir\example\Hello` we
expect you to use:

	class Hello extends \mjolnir\Hello

Instead of this form:

	class Hello extends \mjolnir\example\Hello

This allows us to (if needed) move the `Hello` class to `mjolnir\legacy` with
out breaking your code. Remember this type of loading only works on registered
namespaces and not namespaces available via composer.

The last (and most common) way of resolving the class is via the special `app`
namespace, ie.

	\app\Controller_AcmeOrgnaization

When we resolve a class via the `app` namespace we are always asking for the
most advanced implementation of said class; which simply boils down to which
namespace holding such a class is at the top of the stack in your module
declarations (or as a result of your your module declarations; depending on your
setup). In Mjölnir every use of every class is via the `app` namespace so by
creating a top level class in your application you can replace and/or customize
any class in the system.

The only direct dependencies to the library files are the interfaces which
have been used with explicit namespaces to discourage bad patterns, and
encourage consistency (more on this in the types section).

#### Namespaces must be unique

*Each module may have one namespace, and that namespace you choose must be
unique.*

The namespace must be unique both in the project, and the world. The namespace
must not appear anywhere else, on anything other then this module, even if the
place it appears on is a project that does not rely on the class loading system
described here. If it is PHP code, or can interchange calls with PHP code, it is
an invalid namespace, because it fails to be unique.

To understand why, you have to first understand what problems namespaces solve,
and how they solve them. The are three main problems:

 1. name conflicts with other peoples code
 2. name conflicts with your old code
 3. name conflicts with your yet to be written code

Let's consider the earlier example
`\acme\security\access\Controller_AcmeOrganization` as a benchmark. The
first part of the namespace (ie. `acme`) solves the first problem: it is unique
and can act as a "family" name for the rest of the code. One can thus safely
write any function or class within it with out fear of it conflicting to
one in another unknown library, framework, plugin, etc.

Eventually as the code family grows out we start having problems of managing
name conflicts within it. We can avoid confusion by creating a smaller namespace
within it. Since the `acme` namespace is a blank slate we can choose this time
from common words, so we get the added benefit of organizing our code better at
the same time, which solves the second problem "name conflicts with
your old stuff".

When we grow past this point we can continue to add segments as a means of
separating concerns, so when multiple modules are being created simulataniously
with potentially conflicting class names the code stays safe from potential
reuse of names (ie. there could be a
`\acme\security\protocols\Controller_AcmeOrganization`) by working in the
`acme\security\access` namespace we don't have to care, thus achieving point
three in our initial problems list, future proofing.

Following the above, here are some patterns to avoid.

Namespaces as extentions of the class name, ie. `\acme\Controller\Organization`.
This is very impractical, and mostly abused for purely pointless sugarcoding
purposes. If `Controller` there establishes a sub space and `Organization` is a
controller, then what is a controller in a namespace other then `Controller` in
the same `acme` namespace, other then confusing? In addition, if all controllers
are meant to go into this `Controller` namespace how can you have another
`Organization` controller? The answer is "you can't", neither can you for
practical applications but also mistakenly creating a class with the name
`Organization` is errornous and means you have to be aware of problems 2 & 3
outlined above by yourself, rather then the namespace resolving it for you
(as it should). If that was not enough one has to also consider how the classes
are completely incorrect with this pattern: an `Organization` class might act
the function but it is not very intuitive and nobody will understand it as a
`Controller_Organization` outside the namespace context.

Namespaces should act as a "name space" first, anything else *third*, so we
recommend avoiding these "beautification" patterns.

*Incidentally, the `app` namespace is actually a valid namespace. Even though it
doesn't follow the exact recommendation above, it does meet the requirements due
to how it functions: all classes in it are unique at runtime.*
