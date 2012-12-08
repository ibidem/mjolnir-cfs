In a cascading file system, modules are the foundation blocks for everything
within the system. Without modules the systems can not function.

A module can contain the following,

 1. Classes
 2. Configuration Files
 3. Files, such as Views, Themes, Vendor/3rd-party code, etc
 4. Anything else ("if it fits, it's okey", eg. documentation, drafts, etc)

Classes and configuration files are the first class citizens in a module. The
entire module structure is designed around classes, and configuration files are
merged together, which is different to other files (including classes).

Assuming default structure is used, a module works as follows:

  1. all files are located in directories (on the module root) prefixed
 with a "+"
  2. all configuration files and (files known by the loading process) are stored
 in the main application files directory (by default "+App"), any other
 directories (eg. "+Docs") are not available in the file system.

Under normal conventions

 1. Configuration files are stored in `+App/config`
 2. View files are stored in `+App/views`
 3. Theme files are stored in `+App/themes`
 4. Drafts are stored in `+App/drafts`
 5. Vendor files are stored in `+App/vendor`
 6. Internationalization/grammar files are stored in `+App/lang`
 7. Functions are stored in `+App/functions`
 8. Special classes are stored in `+App/includes`
 9. Behavior tests are stored in `+App/features`
 10. Unit tests are stored in `+App/tests`
 11. Special temporary files are stored in `+App/tmp`

*Note: The `+App/honeypot.php` files are designed to be read by your IDE to
facilitate autocompletion, refactoring, etc; they serve no other purpose and the
only time you should be opening them is when your IDE is failing to scan them.*

To get started with a base structure go to:
<https://github.com/ibidem/mjolnir-template-app> and follow the instructions
outlined in the `README.md` file (github should offer you a parsed version at
the given link).
