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
	function masterlog($level, $info, $ancilary_info = '')
	{
		$conf = \mjolnir\log_settings();

		// check dupliction rules, exclude rules, etc
		if (\mjolnir\log_ignorable($level, $info))
		{
			return;
		}

		$message = $info.$ancilary_info;

		$time = \date('Y-m-d H:i:s');
		$logspath = \app\Env::key('etc.path').'logs/';
		$datepath = \date('Y').'/'.\date('m').'/';
		$masterlogs_path = $logspath.$datepath;

		if (isset($_SERVER, $_SERVER['REMOTE_ADDR']))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		else # no remote addr
		{
			$ip = 'n/a';
		}

		if (isset($_SERVER, $_SERVER['REQUEST_URI']))
		{
			$uri = $_SERVER['REQUEST_URI'];
		}
		else # no remote addr
		{
			$uri = 'n/a';
		}

		if (isset($_SERVER, $_SERVER['HTTP_REFERER']))
		{
			$referer = $_SERVER['HTTP_REFERER'];
		}
		else # no referer
		{
			$referer = 'n/a';
		}

		if (isset($_SERVER, $_SERVER['REQUEST_METHOD']))
		{
			$method = $_SERVER['REQUEST_METHOD'];
		}
		else # no method
		{
			$method = 'n/a';
		}

		// include aditional diagnostic information
		$message
			= \rtrim($message, "\n\r")
			. "\n\t\t-\n"
			. "\t\tURI: $uri\n"
			. "\t\tMethod: $method\n"
			. "\t\tReferer: $referer\n"
			. "\t\tIP: $ip\n"
			;

		// attempt to add user information
		try
		{
			if (\class_exists('\app\Auth', false))
			{
				$message .= "\t\tUser: ".(\app\Auth::id() !== null ? \app\Auth::id() : 'guest')."\n";
			}
			else # not applicable
			{
				$message .= "\t\tUser: n/a\n";
			}
		}
		catch (\Exception $e)
		{
			$message .= "\t\tUser: error\n";
		}

		$message = \sprintf(" %s --- %-13s | %s", $time, $level, $message);

		// append message to master log
		\mjolnir\append_to_file($masterlogs_path, \date('d').'.log', PHP_EOL.$message);

		if ($conf['replication'])
		{
			$replication_path = \rtrim($level, '\\/').DIRECTORY_SEPARATOR;
			\mjolnir\append_to_file($logspath.$replication_path.$datepath, \date('d').'.log', PHP_EOL.$message);
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
		// check exclude rules, etc (but do not check for duplication rules)
		if (\mjolnir\log_ignorable($level, $message, false))
		{
			return;
		}

		$conf = \mjolnir\log_settings();

		if ( ! $conf['devlogs'])
		{
			return;
		}

		$time = \date('Y-m-d H:i:s');
		$logspath = \app\Env::key('etc.path').'logs/';
		$message = \str_replace(\app\Env::key('sys.path'), '', $message);
		$message = \sprintf(" %s --- %-13s | %s", $time, $level, $message);

		// append message to master log
		\mjolnir\append_to_file($logspath, 'short.log', PHP_EOL.$message);
	}
}

if ( ! \function_exists('\mjolnir\log'))
{
	/**
	 * Log is a shorthand for quick shortlog followed by masterlog. With the
	 * exception of ancilary_info the same parameters will be passed on to both.
	 */
	function log($level, $message, $ancilary_info = '')
	{
		\mjolnir\shortlog($level, $message);
		\mjolnir\masterlog($level, $message, $ancilary_info);
	}
}
