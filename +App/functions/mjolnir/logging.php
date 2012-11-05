<?php namespace mjolnir;

#
# Logging is a function to make sure it's available even in the absence of 
# autoloading.
#

require __DIR__.'/utility'.EXT;

if ( ! \function_exists('\mjolnir\masterlog'))
{
	/**
	 * Bread and butter log function. Will always log to the master log, if a
	 * relative path is provided the log entry will be duplicated there as well
	 * for easier reading.
	 */
	function masterlog($level, $message, $replication_path = null, $relative_path = true)
	{
		$time = \date('Y-m-d H:i:s');
		$logs_path = APPPATH.'logs'.DIRECTORY_SEPARATOR;
		$date_path = \date('Y').DIRECTORY_SEPARATOR.\date('m').DIRECTORY_SEPARATOR;
		$master_logs_path = $logs_path.$date_path;
		$message = \sprintf(" %s --- %-12s | %s", $time, $level, $message);
		
		// append message to master log
		\mjolnir\append_to_file($master_logs_path, \date('d').'.log', PHP_EOL.$message);

		if ($replication_path)
		{
			if ($relative_path)
			{
				\mjolnir\append_to_file($logs_path.$replication_path.$date_path, \date('d').'.log', PHP_EOL.$message);
			}
			else # absolute path
			{
				\mjolnir\append_to_file($replication_path.$date_path, \date('d').'.log', PHP_EOL.$message);
			}
		}
	}
}

if ( ! \function_exists('\mjolnir\shortlog'))
{
	/**
	 * Shortlog stores debug info for tail -f usage.
	 * 
	 * Do not use shortlog for spammy errors!
	 * Do not use shortlog with traces!
	 */
	function shortlog($level, $message)
	{
		$time = \date('Y-m-d H:i:s');
		$logs_path = APPPATH.'logs'.DIRECTORY_SEPARATOR;
		$message = \str_replace(DOCROOT, '', $message);
		$message = \sprintf(" %s --- %-12s | %s", $time, $level, $message);
		
		// append message to master log
		\mjolnir\append_to_file($logs_path, 'short.log', PHP_EOL.$message);
	}
}

if ( ! \function_exists('\mjolnir\log'))
{
	/**
	 * Log is a shorthand for quick shortlog followed by masterlog with the same
	 * parameters.
	 */
	function log($level, $message, $replication_path = null, $relative_path = true)
	{
		\mjolnir\shortlog($level, $message);
		\mjolnir\masterlog($level, $message, $replication_path, $relative_path);
	}
}