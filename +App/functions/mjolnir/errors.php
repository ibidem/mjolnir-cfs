<?php namespace mjolnir;

if ( ! \function_exists('\mjolnir\log_exception'))
{
	function log_exception($exception, $replication_path = 'Exceptions/')
	{
		if (\is_array($exception))
		{
			\mjolnir\log_error($exception);
			return;
		}
		
		$error_diagnostic = $exception->getMessage().' ('.\ltrim(\str_replace(\rtrim(DOCROOT, '/\\'), '', $exception->getFile()), '/\\').' @ '.$exception->getLine().')';

		// include trace
		$error_diagnostic .= "\n";
		$error_diagnostic .= \mjolnir\implode("\n", \array_reverse($exception->getTrace()), function ($idx, $step) {
			if (isset($step['file'], $step['line']))
			{
				// build info
				$info = '';
				if (isset($step['function']))
				{
					$info = ', Function: '.$step['function'];
				}

				if (isset($step['args']))
				{
					$info = ', Arguments: { '.\mjolnir\implode(', ', $step['args'], function ($key, $v) {
						return $key.' => '. \mjolnir\stringify($v);
					}).' }';
				}

				return "\t\t".\sprintf('%2d. %s', $idx+1, \ltrim(\str_replace(\rtrim(DOCROOT, '/\\'), '', $step['file']), '/\\').' @ Line '.$step['line'].$info);
			}
			else # anonymous function
			{
				return "\t\t".\sprintf('%2d. %s', $idx+1, '[Closure]');
			}
		});
		$error_diagnostic .= "\n";

		if (\class_exists('\app\Log'))
		{
			// log the error
			\mjolnir\log('Exception', $error_diagnostic, $replication_path);
		}
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
				\mjolnir\log('Error', $error['message'].' In "'.\ltrim(\str_replace(\rtrim(DOCROOT, '/\\'), '', $error['file']), '/\\').'" @ Line '.$error['line'], 'Errors/');
			}
			else # unknown format
			{
				\mjolnir\log('Error', \serialize($error), 'Errors/');
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
				\mjolnir\log('Error', $error->getMesssage(), 'Errors/');
			}
			else # unprocessable
			{
				\mjolnir\log('Error', 'Unprocessable error. Serialization: '.\serialize($error), 'Errors/');
			}
		}
	}
}

if ( ! \function_exists('\mjolnir\exception_handler'))
{
	function exception_handler($exception)
	{
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

		\mjolnir\log_exception($exception);
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
					\header('Location: '.$error_page) ;
				}
			}
			catch (\Exception $e)
			{
				\mjolnir\log_exception($exception);
			}
		}
	}
}
