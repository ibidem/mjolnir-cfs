<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Status extends \app\Task_Base
{
	/**
	 * Attempt to detect php binary location.
	 *
	 * @return string
	 */
	private function php_binary()
	{
		$paths = \explode(PATH_SEPARATOR, \getenv('PATH'));
		foreach ($paths as $path)
		{
			$php_executable = $path . DIRECTORY_SEPARATOR . 'php' . (isset($_SERVER['WINDIR']) ? '.exe' : '');
			if (\file_exists($php_executable) && \is_file($php_executable)) {
				return $php_executable;
			}
		}

		return 'undetectable; try the command: which php'; // not found
	}

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		// PHP_BINARY is avaiable from php5.4+
		if ( ! \defined('PHP_BINARY'))
		{
			\define('PHP_BINARY', self::php_binary());
		}

		$this->writer->printf('title', 'PHP: '.PHP_BINARY);

		// if the checking should stop on error
		$no_stop = $this->get('no-stop', false);
		$strict = $this->get('strict', false);

		// load requirements
		$modules = \app\CFS::config('mjolnir/require');

		$failed = 0;
		$errors = 0;

		foreach ($modules as $module => $requirements)
		{
			$this->writer->writef(" %s", $module)->eol()->eol();
			foreach ($requirements as $requirement => $tester)
			{
				try
				{
					 $statusinfo = $tester();
				}
				catch (\Exception $e)
				{
					\mjolnir\log_exception($e);
					$statusinfo = 'untestable';
				}

				if (\is_array($statusinfo))
				{
					$status = \key($statusinfo);
					$statushint = \current($statusinfo);
				}
				else # non-array status
				{
					$status = $statushint = $statusinfo;
				}

				switch ($status)
				{
					case 'error':
						$this->writer->printf('status', $statushint, $requirement)->eol();
						! $no_stop or self::error();
						++$errors;
						break;
					case 'failed':
						$this->writer->printf('status', $statushint, $requirement)->eol();
						( ! $strict && ! $no_stop) or self::error();
						++$failed;
						break;
					case 'available':
						$this->writer->printf('status', 'passed', $requirement)->eol();
						break;
					case 'satisfied':
						$this->writer->printf('status', 'passed', $requirement)->eol();
						break;
					case 'untestable':
						$this->writer->printf('status', $statushint, $requirement)->eol();
						! $no_stop or self::error();
						++$errors;
						break;
					default:
						$this->writer->printf('status', 'untestable', $requirement)->eol();
						! $no_stop or self::error();
						++$errors;
						break;
				}
			}
			$this->writer->eol();
		}

		if ($failed + $errors === 0)
		{
			$this->writer->eol()
				->writef(' PASSED. Modules running optimally.')->eol();
		}
		else if ($failed > 0 && $errors === 0)
		{
			$this->writer->eol()
				->writef(' PASSED, but '.$failed.' test'.( $failed == 1 ? '' : 's').' failed.')->eol()
				->eol()
				->writef(' Failed modules will run using fallbacks; or functionality may be limited.')->eol();
		}
		else if ($errors > 0)
		{
			$this->writer->eol()->writef(' FAILED DEPENDENCIES ')->eol();
			exit(1);
		}
	}

	/**
	 * Exit with failed message
	 */
	private function error()
	{
		$this->writer
			->eol()
			->writef(' FAILED DEPENDENCIES ')->eol()
			->eol()
			->printf('status', 'Help', 'Make sure the php you\'re running the command with is the same php the server is using!')->eol();

		exit(1);
	}

} # class