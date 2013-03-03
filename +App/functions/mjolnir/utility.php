<?php namespace mjolnir;

#
# Functions used in critical sections where autoloading can not be relied on.
#

if ( ! \function_exists('\mjolnir\append_to_file'))
{
	/**
	 * File helper.
	 */
	function append_to_file($path, $file, $data)
	{
		try
		{
			if ( ! \file_exists($path))
			{
				@\mkdir($path, 02775, true);
				@\chmod($path, 02775);

				if ( ! \file_exists($path.$file))
				{
					// Create the log file
					@\file_put_contents($path.$file, PHP_EOL);

					// Allow anyone to write to log files
					@\chmod($path.$file, 0666);
				}
			}

			@\file_put_contents($path.$file, $data, FILE_APPEND);
		}
		catch (\Exception $exception)
		{
			\mjolnir\log_exception($exception);

			if (\defined('PUBDIR'))
			{
				// attempt to retrieve configuration
				try
				{
					$config = \file_get_contents(PUBDIR.'config.php');

					if ($config['development'])
					{
						throw $exception;
					}

					// non development environment; prevent catastrophic failure
					return;
				}
				catch (\Exception $e)
				{
					// we can't do anything at this point but prevent
					// catastrophic failure

					return;
				}
			}
			else # non-web context
			{
				throw $exception;
			}
		}
	}
}

if ( ! \function_exists('\mjolnir\implode'))
{
	/**
	 * Function based impode.
	 */
	function implode($glue, array $list, callable $manipulator)
	{
		$glued = '';
		foreach ($list as $key => $value)
		{
			$glued .= $glue.$manipulator($key, $value);
		}

		$glued = \substr($glued, \strlen($glue));

		return $glued;
	}
}

if ( ! \function_exists('\mjolnir\array_merge'))
{
	/**
	 * Function version of CFS::config_merge.
	 */
	function array_merge(array &$base, array &$overwrite)
	{
		foreach ($overwrite as $key => &$value)
		{
			if (\is_int($key))
			{
				// add only if it doesn't exist
				if ( ! \in_array($overwrite[$key], $base))
				{
					$base[] = $overwrite[$key];
				}
			}
			else if (\is_array($value))
			{
				if (isset($base[$key]) && \is_array($base[$key]))
				{
					array_merge($base[$key], $value);
				}
				else # does not exist or it's a non-array
				{
					$base[$key] = $value;
				}
			}
			else # not an array and not numeric key
			{
				$base[$key] = $value;
			}
		}
	}
}

if ( ! \function_exists('\mjolnir\log_settings'))
{
	/**
	 * Re-configure or retrieve log settings by passing null.
	 */
	function log_settings(array $new_settings = null)
	{
		static $settings = array
			(
				// turning duplication will cause the logging system to relog
				// re-occuring errors based on their main exception message hash
				// with the option off only the first occurance will be recorded
				'duplication' => false,

				// the logging system will replicate all errors based on their
				// level key. So "Notice" errors will get replicated into Notice
				// Hacking will get replicated into Hacking. This can be very
				// efficient way of managing your log. For integrity reasons the
				// master log will still hold the errors regardless
				'replication' => false,

				// the short.log or devlog stores a 1-line version of the error
				// this is very useful in development where most errors can be
				// easily identified in a few words and don't need to be stored
				'short.log' => true,

				// you may ignore certain types of log errors if you already
				// an alternative system in place that catches them and reports
				// them to you; one such case are 404 errors
				'exclude' => array
					(
						// empty
					),

				// sometimes you may be recieving errors from underlying or
				// proxy systems outside your control. Often these are caused
				// by broken client side javascript, that makes its way to the
				// the server, insert any regular expression bellow if the main
				// message matches the pattern it will be ignored
				'filter' => array
					(
						// no regex patterns
					),
			);

		try
		{
			if ($new_settings !== null)
			{
				\mjolnir\array_merge($settings, $new_settings);
			}

			return $settings;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
}

if ( ! \function_exists('\mjolnir\log_cache'))
{
	/**
	 * Re-configure or retrieve log settings by passing null. APC will be used
	 * if available, otherwise a file based cache will be created.
	 */
	function log_cache(array $new_cache = null)
	{
		static $cache = null;

		#
		# We intentionally don't refresh the local cache when we update the
		# cache to prevent cross-cache writes when multiple loggers are
		# requesting cache info.
		#

		if ($new_cache !== null)
		{
			if (\function_exists('\apc_store') && \function_exists('\apc_fetch'))
			{
				\apc_store('mjolnir:log.cache', $new_cache, 0);
			}
			else # file based cache
			{
				@\file_put_contents(ETCPATH.'cache/log.cache', \serialize($new_cache));
			}

		}
		else # retrieve cache
		{
			if ($cache === null)
			{
				// initialize cache
				if (\function_exists('\apc_store') && \function_exists('\apc_fetch'))
				{
					$successful = false;
					$cache = \apc_fetch('mjolnir:log.cache', $successful);
					
					if ( ! $successful)
					{
						$cache = [];
					}
				}
				else # file based cache
				{
					if (\file_exists(ETCPATH.'cache/log.cache'))
					{
						$cache = \unserialize(\file_get_contents(ETCPATH.'cache/log.cache'));
					}
					else # empty cache
					{
						$cache = [];
					}
				}
			}

			return $cache;
		}
	}
}

if ( ! \function_exists('\mjolnir\log_ignorable'))
{
	/**
	 * Check duplication rules, exclude rules, etc.
	 */
	function log_ignorable($level, $message, $duplication_check = true)
	{
		$conf = \mjolnir\log_settings();

		if (\in_array($level, $conf['exclude']))
		{
			return true;
		}

		foreach ($conf['filter'] as $pattern)
		{
			if (\preg_match($pattern, $message))
			{
				return true;
			}
		}

		if ($duplication_check && ! $conf['duplication'])
		{
			$hash = \md5($level.$message);
			$cache = \mjolnir\log_cache();

			if (\in_array($hash, $cache))
			{
				return true;
			}
			else # uncached
			{
				// we cache it
				$cache[] = $hash;
				\mjolnir\log_cache($cache);
			}
		}

		// passed all tests
		return false;
	}
}
