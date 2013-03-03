<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Cleanup extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * Clear cache.
	 */
	function cache_cleanup()
	{
		$base = \app\CFS::config('mjolnir/base');
		$cache_settings = $base['caching'];
		$base['caching'] = true;

		$this->writer->writef('  - flushing file cache')->eol();
		\app\Stash_File::instance()->flush();
		$this->writer->writef('  - flushing memcache cache')->eol();
		\app\Stash_Memcache::instance()->flush();
		$this->writer->writef('  - flushing memcached cache')->eol();
		\app\Stash_Memcached::instance()->flush();
		$this->writer->writef('  - flushing apc cache')->eol();
		\app\Stash_APC::instance()->flush();

		$base['caching'] = $cache_settings;
	}

	/**
	 * Execute task.
	 */
	function run()
	{
		$pruge_logs = $this->get('purge-logs', false);

		\app\Task::consolewriter($this->writer);

		$this->writer->writef(' Reseting cache')->eol();
		$this->cache_cleanup();
		$this->writer->eol();

		# Remove Log files

		if ($pruge_logs)
		{
			$this->writer->writef(' Removing logs')->eol();
			$log_files = \app\Filesystem::matchingfiles(ETCPATH.'logs', '#^[^\.].*$#');

			foreach ($log_files as $file)
			{
				$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
				try
				{
					\unlink($file);
				}
				catch (\Exception $e)
				{
					$this->writer->writef('    failed to remove '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
				}
			}

			\app\Filesystem::prunedirs(ETCPATH.'logs');
		}

		# Remove Cache files

		$this->writer->writef(' Removing misc cache files')->eol();
		$cache_files = \app\Filesystem::matchingfiles(ETCPATH.'cache', '#^[^\.].*$#');

		foreach ($cache_files as $file)
		{
			$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
			try
			{
				\unlink($file);
			}
			catch (\Exception $e)
			{
				$this->writer->writef('    failed to remove '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
			}
		}

		\app\Filesystem::prunedirs(ETCPATH.'cache');

		# Remove Temporary file

		$this->writer->writef(' Removing temporary files')->eol();
		$tmp_files = \app\Filesystem::matchingfiles(ETCPATH.'tmp', '#^[^\.].*$#');

		foreach ($tmp_files as $file)
		{
			$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
			try
			{
				\unlink($file);
			}
			catch (\Exception $e)
			{
				$this->writer->writef('    failed to remove '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
			}
		}

		\app\Filesystem::prunedirs(ETCPATH.'tmp');
	}

} # class
