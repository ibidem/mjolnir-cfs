In the following section we'll cover creating a basic application. This is the
fastest way to get up and running, but if you wish you may skip the section and
go into specific sections. If you wish to have a good technical understanding
on types used you may skip all the way to the types section which explains all
types in the system.

In the following tutorial some of the paths may be changed for easier
development; for correctness server optimal structure is illustrated.

For clarity we are going to assume `~/www` points to your server's public
directory. We're also going to assume we are creating our project in `~/demo`
and the project is called "Demo" and our root namespace for the project is
"demo." Replace paths with your own and feel free to replace names with your own
as well. We'll use `~` for home directory paths but in cases where the path
needs to be absolute we'll assume `~` to be `/home/site_user/`. We are also
going to assume development is done on localhost, so the domain in question for
our demo is `127.0.0.1` (note: there are complications with using the
`localhost` variant in some browsers; the choice here is not just
personal preference).

In the tutorial we will cover all commands and details on what's happening.
Keep in mind that the time to complete the tutorial (ie. read, copy commands,
etc) is not representative of the time it will take you to repeat it on a real
project from scratch. We will also illustrate how to perform some basic
troubleshooting and cover errors you might encounter which will add several
"dead steps" to the process; we find it important you be aware how to not get
bogged down, but these also add significant time to the process.

For the sake of brevity we will assume you are familiar with `PHP` and `git`
and will only cover what we consider potentially non-intuitive details.

For the purpose of this tutorial you should have the following installed on
your development machine: git, PHP (with console access), Ruby (1.9.x generally),
Sass (`gem install sass`), Ruby Zip (`gem install rubyzip`), java (used for
compiling javascript with google's closure compiler), a server

On windows we recommend using *git bash* for the tutorials, it will give you
access to a unix style command line and tools. Recommended servers on windows
are Uniform Server, EasyPHP. nginx based stacks are available but we'll be
assuming apache servers for simplicity sake.
