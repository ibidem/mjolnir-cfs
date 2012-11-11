There are a few ways to load files known by the system. The first way, which is
the most convenient for single files, is to use `CFS::file($file, $ext = EXT)`
this will search all modules from top to bottom and stop when it finds a file;
so it will give you the top file in the module stack.

Another way to load files is to load a file via its directory; this is mostly
done with vendor/3rd-party code since we want some gurantee we're getting the
right "config.php" and we don't really care for the file itself per se as much
as we do about getting it from the correct directory. The method for this is
`CFS::dir($directory)` and a simple use case example would look like this:

	require_once \app\CFS::dir('vendor/awesomesomething').'mainclass.inc';

If we need all the files of the given name we use
`CFS::file_list($file, $ext = EXT)` which functions almost the same way as
`CFS::file` only instead of the top file we'll get back and array of all the
matching files.

If we need to have a more sophisticated search, we can use
`CFS::find_files($pattern, array $contexts = null, array & $matches = [])`. This
function can be used even outside the context of the cascading file system by
simply providing different contexts in it (in the absence of any contexts, it
will default to searching all registered file paths).

A different way to get to files is by retrieving the path and doing your own
handling. You can get the path to the module's root via
`CFS::modulepath($namespace)` (or the practically equivalent method for standard
modules `CFS::classpath($namespace)`), and the file path via
`CFS::filepath($namespace)`.
