<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Cleanup extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);
		
		$this->writer->writef(' Reseting cache.')->eol();
		\app\Stash_File::instance()->flush();
		\app\Stash_Memcache::instance()->flush();
		\app\Stash_Memcached::instance()->flush();
		
		# Remove Log files
		
		$this->writer->writef(' Removing logs.')->eol();	
		$log_files = \app\Filesystem::matchingfiles(APPPATH.'logs', '#^[^\.].*$#');
		
		foreach ($log_files as $file)
		{
			$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
			\unlink($file);
		}
		
		\app\Filesystem::prunedirs(APPPATH.'logs');
		
		# Remove Cache files
		
		$this->writer->writef(' Removing cache files.')->eol();
		$cache_files = \app\Filesystem::matchingfiles(APPPATH.'cache', '#^[^\.].*$#');
		
		foreach ($cache_files as $file)
		{
			$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
			\unlink($file);
		}
		
		\app\Filesystem::prunedirs(APPPATH.'cache');
		
		# Remove Temporary file
		
		$this->writer->writef(' Removing temporary files.')->eol();
		$tmp_files = \app\Filesystem::matchingfiles(APPPATH.'tmp', '#^[^\.].*$#');
		
		foreach ($tmp_files as $file)
		{
			$this->writer->writef('  - removing '. \str_replace('\\', '/', \str_replace(DOCROOT, '', $file)))->eol();
			\unlink($file);
		}
		
		\app\Filesystem::prunedirs(APPPATH.'tmp');
	}

} # class
