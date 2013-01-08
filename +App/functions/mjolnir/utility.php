<?php namespace mjolnir;

#
# Functions used in critical sections where autoloading can not be relied on.
#

if ( ! \function_exists('\mjolnir\append_to_file'))
{
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
	function implode($glue, array $list, $f_key_values)
	{
		$glued = '';
		foreach ($list as $key => $value)
		{
			$glued .= $glue.$f_key_values($key, $value);
		}

		$glued = \substr($glued, \strlen($glue));

		return $glued;
	}
}