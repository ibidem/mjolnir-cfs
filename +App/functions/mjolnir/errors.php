<?php namespace mjolnir;

if ( ! \function_exists('\mjolnir\log_exception'))
{
	function log_exception(\Exception $exception, $replication_path = 'Exceptions/')
	{
		$error_diagnostic 
			= $exception->getMessage()
			. ' ('.\str_replace(DOCROOT, '', $exception->getFile()).' @ Ln '.$exception->getLine().')'
			;
		
		\mjolnir\shortlog('Exception', $error_diagnostic);
		
		// compute stack trace
		
		$trace = $exception->getTraceAsString();
		
		$error_diagnostic 
			.= \str_replace
				(
					DOCROOT, 
					'', 
					\str_replace
						(
							PHP_EOL, 
							PHP_EOL."\t\t", 
							PHP_EOL.\trim($trace, '\'')
						)
				)
			. PHP_EOL # extra line for clear seperation of traces
			;

		// main log
		\mjolnir\masterlog('Exception', $error_diagnostic, $replication_path);
	}
}

if ( ! \function_exists('\mjolnir\log_error'))
{
	function log_error($error)
	{
		if (\is_array($error))
		{
			if (isset($error['message'], $error['file'], $error['line']))
			{
				$error_diagnostic 
					= $error['message']
					. '('
					. \str_replace(DOCROOT, '', $error['file'])
					. ' @ Ln '.$error['line']
					. ')'
					;

				\mjolnir\log('FatalError', $error_diagnostic, 'FatalErrors/');
			}
			else # unknown format
			{
				\mjolnir\log('FatalError', \serialize($error), 'FatalErrors/');
			}
		}
		else if (\is_a('\Exception', $error))
		{
			\mjolnir\log_exception($error);
		}
		else
		{
			$error_methods = \get_class_methods($error);

			if (\count(\array_intersect(['getMessage', 'getTrace', 'getFile', 'getLine'], $error_methods)) === 4)
			{
				\mjolnir\log_exception($error);
				return;
			}

			if (\in_array('getMessage', $error_methods))
			{
				\mjolnir\log('FatalError', $error->getMesssage(), 'FatalErrors/');
			}
			else # unprocessable
			{
				\mjolnir\log('FatalError', 'Unprocessable error. Serialization: '.\serialize($error), 'FatalErrors/');
			}
		}
	}
}

if ( ! \function_exists('\mjolnir\exception_handler'))
{
	function exception_handler($exception)
	{
		\mjolnir\log_exception($exception);

		$base_config = \app\CFS::config('mjolnir/base');

		if ( ! empty($base_config) && \app\CFS::config('mjolnir/base')['development'])
		{
			echo 'Uncaught Exception';
			if (\app\Layer::find('http'))
			{
				echo "<pre>\n";
			}

			echo $exception->getMessage()
				. "\n".\str_replace(DOCROOT, '', $exception->getTraceAsString());
		}
		else # public version
		{
			if (\defined('PUBDIR'))
			{
				include PUBDIR.'error'.EXT;
			}
			else if (\php_sapi_name() === 'cli')
			{
				echo 'Uncaught Exception';
				echo $exception->getMessage()
				. "\n".\str_replace(DOCROOT, '', $exception->getTraceAsString());
			}
			else # unknown
			{
				echo 'Uncaught Exception';
			}
		}
	}
}

if ( ! \function_exists('\mjolnir\error_handler'))
{
	function error_handler($errno, $errstr, $errfile, $errline)
	{
		 throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
	}
}

if ( ! \function_exists('\mjolnir\shutdown_error_checks'))
{
	function shutdown_error_checks()
	{
		$exception = \error_get_last();

		if ($exception !== null)
		{
			\mjolnir\log_error($exception);

			try
			{
				if (\defined('PUBDIR'))
				{
					$base_config = include PUBDIR.'config'.EXT;
					$error_page = '//'.$base_config['domain'].$base_config['path'].'error'.EXT;
					\header('Location: '.$error_page);
				}
			}
			catch (\Exception $e)
			{
				\mjolnir\log_exception($e);
				// potentially headers already sent; attempt to redirect via javascript
				echo '<script type="text/javascript">window.location = "'.$error_page.'"</script>';
			}
		}
	}
}
