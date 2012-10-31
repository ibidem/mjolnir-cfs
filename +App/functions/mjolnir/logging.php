<?php namespace mjolnir;

#
# Logging is a function to make sure it's available even in the absence of 
# autoloading. The \app\Log::message is merely syntactic sugar for this function
#

require __DIR__.'/utility'.EXT;

if ( ! \function_exists('\mjolnir\log'))
{
	function log($level, $message, $replication_path = null, $relative_path = true)
	{
		$time = \date('Y-m-d H:i:s');
		$logs_path = APPPATH.'logs'.DIRECTORY_SEPARATOR;
		$date_path = \date('Y').DIRECTORY_SEPARATOR.\date('m').DIRECTORY_SEPARATOR;
		$master_logs_path = $logs_path.$date_path;
		$message = \sprintf(" %s --- %-10s | %s", $time, $level, $message);
		
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
	function shortlog($level, $message)
	{
		$time = \date('Y-m-d H:i:s');
		$logs_path = APPPATH.'logs'.DIRECTORY_SEPARATOR;
		$message = \sprintf(" %s --- %-10s | %s", $time, $level, $message);
		
		// append message to master log
		\mjolnir\append_to_file($logs_path, 'short.log', PHP_EOL.$message);
	}
}
