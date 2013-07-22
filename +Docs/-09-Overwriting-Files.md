To overwrite files simple place another file with the same name in a higher
priority module.

When dealing with vendor files it's usually a good idea to place them in a
directory vendor inside the `+App` folder (ie. general files folder), in
their own folder then use `\app\CFS::dir` to pull them in.

	require_once \app\CFS::dir('vendor/the_vendor').'main_class.php';

The reason for doing this is so you can overwrite the folder instead of the
file though in the case of most vendors dependencies that load everything
manually you'll get semi-equivalent results if searching for the file or
searching for the folder then append the file like above.
