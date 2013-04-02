<?
	namespace app;
	
	/* @var $theme ThemeView */
?>

<p>
	If a directory is unreadable, then its contents are considered unwritable, unreadable, unexecutable. 
	These directories will appear in all listings
</p>

<p>
	<span class="label label-info">Tip</span> <small>On *nix systems the directory permissions have different meaning, compared to file permissions.</small>
</p>

<h2>Unreadable</h2>

<?
	$files = Filesystem::find_unreadable(Env::key('sys.path'), '#.#'); 
	$files = Arr::merge($files, Filesystem::find_unreadable(Env::key('www.path'), '#.#'));
	$files = \array_unique($files);
?>

<table class="table table-striped">
	<tbody>
		<? if (empty($files)): ?>
			<tr><td class="success">No unreadable files found.</td></tr>
		<? else: # got unreadable files ?>
			<? foreach ($files as $file): ?>
				<tr><td><?= $file ?></td></tr>
			<? endforeach; ?>
		<? endif;?>
	</tbody>
</table>

<h2>Unwritable</h2>

<?
	// we only check locations that need to be writable by the server,
	// while technically all system directories need to be writable 
	// since all those cases also require a console they can be 
	// circumvented by requesting temporary higher access to perform
	// the operation, or simply logging in though a user with sufficient
	// privilages

	$files = Filesystem::find_unwritable(Env::key('etc.path').'tmp/', '#.#'); 
	$files = Arr::merge($files, Filesystem::find_unwritable(Env::key('etc.path').'cache/', '#.#'));
	$files = Arr::merge($files, Filesystem::find_unwritable(Env::key('etc.path').'logs/', '#.#'));
	$files = Arr::merge($files, Filesystem::find_unwritable(Env::key('www.path').'uploads/', '#.#'));
	$files = Arr::merge($files, Filesystem::find_unwritable(Env::key('www.path').'media/', '#.#'));
	$files = \array_unique($files);
?>

<table class="table table-striped">
	<tbody>
		<? if (empty($files)): ?>
			<tr><td class="success">No unwritable files found.</td></tr>
		<? else: # got unreadable files ?>
			<? foreach ($files as $file): ?>
				<tr><td><?= $file ?></td></tr>
			<? endforeach; ?>
		<? endif;?>
	</tbody>
</table>

<h2>Unexecutable</h2>

<? if (\strtolower(\substr(PHP_OS, 0, 3)) !== 'win'): ?>

	<?
		$files = Filesystem::find_unexecutable(Env::key('sys.path'), '#^\+compile\.rb$#');
		$files = Arr::merge($files, Filesystem::find_unexecutable(Env::key('sys.path'), '#^start\.rb$#'));
		$files = Arr::merge($files, Filesystem::find_unexecutable(Env::key('sys.path').'bin/', '#.#'));
		$files = Arr::merge($files, Filesystem::find_unexecutabledir(Env::key('sys.path')));
		$files = \array_unique($files);
	?>

	<table class="table table-striped">
		<tbody>
			<? if (empty($files)): ?>
				<tr><td class="success">No unexecutable files found.</td></tr>
			<? else: # got unreadable files ?>
				<? foreach ($files as $file): ?>
					<tr><td><?= $file ?></td></tr>
				<? endforeach; ?>
			<? endif;?>
		</tbody>
	</table>

<? else: # windows ?>

	<div class="alert alert-info">Executable status of files can not be reliably tested on windows.</div>

<? endif; ?>