<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Cleanup extends \app\Task_Base
{
	/**
	 * Clear cache.
	 */
	function cache_cleanup()
	{
		$this->writer->writef('  - dropping CFS cache')->eol();
		\app\CFS::dropcache();
		$this->writer->writef('  - flushing file cache')->eol();
		\app\Stash_File::instance(false)->flush();
		$this->writer->writef('  - flushing memcache cache')->eol();
		\app\Stash_Memcache::instance(false)->flush();
		$this->writer->writef('  - flushing memcached cache')->eol();
		\app\Stash_Memcached::instance(false)->flush();
		$this->writer->writef('  - flushing apc cache')->eol();
		\app\Stash_APC::instance(false)->flush();
		$this->writer->writef('  - flushing temp memory')->eol();
		\app\Stash_TempMemory::instance(false)->flush();
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
			$log_files = \app\Filesystem::matchingfiles(\app\Env::key('etc.path').'logs', '#^[^\.].*$#');

			foreach ($log_files as $file)
			{
				$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(\app\Env::key('sys.path'), '', $file)))->eol();
				try
				{
					\unlink($file);
				}
				catch (\Exception $e)
				{
					$this->writer->writef('    failed to remove '. \str_replace('\\', '/', \str_replace(\app\Env::key('sys.path'), '', $file)))->eol();
				}
			}

			\app\Filesystem::prunedirs(\app\Env::key('etc.path').'logs');
		}

		# Remove Cache files

		$this->writer->writef(' Removing misc cache files')->eol();
		$cache_files = \app\Filesystem::matchingfiles(\app\Env::key('etc.path').'cache', '#^[^\.].*$#');

		foreach ($cache_files as $file)
		{
			$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(\app\Env::key('sys.path'), '', $file)))->eol();
			try
			{
				\unlink($file);
			}
			catch (\Exception $e)
			{
				$this->writer->writef('    failed to remove '. \str_replace('\\', '/', \str_replace(\app\Env::key('sys.path'), '', $file)))->eol();
			}
		}

		\app\Filesystem::prunedirs(\app\Env::key('etc.path').'cache');

		# Remove Temporary file

		$this->writer->writef(' Removing temporary files')->eol();
		$tmp_files = \app\Filesystem::matchingfiles(\app\Env::key('tmp.path'), '#^[^\.].*$#');

		foreach ($tmp_files as $file)
		{
			$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(\app\Env::key('sys.path'), '', $file)))->eol();
			try
			{
				\unlink($file);
			}
			catch (\Exception $e)
			{
				$this->writer->writef('    failed to remove '. \str_replace('\\', '/', \str_replace(\app\Env::key('sys.path'), '', $file)))->eol();
			}
		}

		\app\Filesystem::prunedirs(\app\Env::key('tmp.path'));
	}

} # class
