<?
	namespace app;
	
	/* @var $theme ThemeView */
	
	// scanning the filesystem can be intensive
	\set_time_limit(60);
?>

<h1>File Permissions</h1>

<p><i>Thousands of files are scanned when you access this section. Expect longer then normal loading times.</i></p>

<p>
	Each section in the listings bellow tests for a specific kind of permission problem in all relevant system files. 
	Any files that are listed should be inspected for errors and fix'ed or otherwise they will eventually destabilize the system.
	You should periodically check back to verify no part of the system is writing files with faulty permissions; highly recommended when you install or implement new components.
</p>

<p>
	If a directory is unreadable then its contents will be considered unwritable, unreadable and unexecutable due to it breaking blind traversal. 
	By consequence these directories may appear in all listings.
</p>

<p>
	<span class="label label-info">Tip</span> <small>On *nix systems read/write/execute permissions have completely different meaning on directories compared to normal files.</small>
</p>

<hr/>

<?
	$stable = true;
?>

<style type="text/css">
	.perm-fixed-column { width: 85px; }
</style>

<? View::frame() ?>

	<h2>Unreadable</h2>

	<p>The <code>sys.path</code>, <code>www.path</code> along with all additional file paths must be completely readable.</p>

	<?
		$files = Filesystem::find_unreadable(Env::key('sys.path'), '#.#');
		foreach (CFS::paths() as $filepath)
		{
			$files = Arr::merge($files, Filesystem::find_unreadable($filepath, '#.#'));
		}
		$files = Arr::merge($files, Filesystem::find_unreadable(Env::key('www.path'), '#.#'));
		$files = \array_unique($files);
		
		if ( ! empty($files))
		{
			$stable = false;
		}
	?>

	<table class="table table-striped">
		<tbody>
			<? if (empty($files)): ?>
				<tr><td><i class="text-success">No unreadable files found.</i></td></tr>
			<? else: # got unreadable files ?>
				<? foreach ($files as $file): ?>
					<tr>
						<td class="perm-fixed-column"><?= \is_dir($file) ? '<b>dir</b>' : '' ?></td>
						<td><?= $file ?></td>
						<td class="perm-fixed-column"><?= Filesystem::permissions($file) ?></td>
						<td class="perm-fixed-column"><?= Filesystem::ownername($file) ?></td>
						<td class="perm-fixed-column"><?= Filesystem::groupname($file) ?></td>
					</tr>
				<? endforeach; ?>
			<? endif;?>
		</tbody>
	</table>

	<h2>Unwritable</h2>
	
	<p><code>etc.path/tmp</code>, <code>etc.path/logs</code>, <code>etc.path/cache</code>, <code>www.path/uploads</code>, <code>www.path/media</code> must be writable.</p>

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
		
		if ( ! empty($files))
		{
			$stable = false;
		}
	?>

	<table class="table table-striped">
		<tbody>
			<? if (empty($files)): ?>
				<tr><td><i class="text-success">No unwritable files found.</i></td></tr>
			<? else: # got unreadable files ?>
				<? foreach ($files as $file): ?>
					<tr>
						<td class="perm-fixed-column"><?= \is_dir($file) ? '<b>dir</b>' : '' ?></td>
						<td><?= $file ?></td>
						<td class="perm-fixed-column"><?= Filesystem::permissions($file) ?></td>
						<td class="perm-fixed-column"><?= Filesystem::ownername($file) ?></td>
						<td class="perm-fixed-column"><?= Filesystem::groupname($file) ?></td>
					</tr>
				<? endforeach; ?>
			<? endif;?>
		</tbody>
	</table>

	<h2>Unexecutable</h2>
	
	<p>All <code>+compile.rb</code>, <code>start.rb</code>, files located in <code>sys.path/bin</code> along with all <i>directories</i> in the <code>sys.path</code>, <code>www.path</code> and any additional paths, must be executable.</p>

	<? if (\strtolower(\substr(PHP_OS, 0, 3)) !== 'win'): ?>

		<?
			$files = Filesystem::find_unexecutable(Env::key('sys.path'), '#^\+compile\.rb$#');
			$files = Arr::merge($files, Filesystem::find_unexecutable(Env::key('sys.path'), '#^start\.rb$#'));
			$files = Arr::merge($files, Filesystem::find_unexecutable(Env::key('sys.path').'bin/', '#.#'));
			$files = Arr::merge($files, Filesystem::find_unexecutabledir(Env::key('sys.path')));
			$files = Arr::merge($files, Filesystem::find_unexecutabledir(Env::key('www.path')));
			foreach (CFS::paths() as $filepath)
			{
				$files = Arr::merge($files, Filesystem::find_unexecutabledir($filepath));
			}
			$files = \array_unique($files);
			
			if ( ! empty($files))
			{
				$stable = false;
			}
		?>

		<table class="table table-striped">
			<tbody>
				<? if (empty($files)): ?>
					<tr><td><i class="text-success">No unexecutable files found.</i></td></tr>
				<? else: # got unreadable files ?>
					<? foreach ($files as $file): ?>
						<tr>
							<td class="perm-fixed-column"><?= \is_dir($file) ? '<b>dir</b>' : '' ?></td>
							<td><?= $file ?></td>
							<td class="perm-fixed-column"><?= Filesystem::permissions($file) ?></td>
							<td class="perm-fixed-column"><?= Filesystem::ownername($file) ?></td>
							<td class="perm-fixed-column"><?= Filesystem::groupname($file) ?></td>
						</tr>
					<? endforeach; ?>
				<? endif;?>
			</tbody>
		</table>

	<? else: # windows ?>

		<p class="text-info">The executable status of files and directories can not be reliably tested on windows.</p>

	<? endif; ?>
		
<? $view = View::endframe() ?>

<? if ($stable): ?>
	<p class="alert alert-success"><big>File Permission Status: <strong>Stable</strong></big></p>
<? else: # unstable ?>
	<p class="alert alert-error"><big>File Permission Status: <strong>Unstable</strong></big></p>
<? endif; ?>

<?= $view ?>
