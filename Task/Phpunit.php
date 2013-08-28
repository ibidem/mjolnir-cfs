<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Task
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Phpunit extends \app\Task_Base
{
	/**
	 * Shorthand command.
	 */
	function run()
	{
		$path = $this->get('path', false);
		$consistent = $this->get('consistent', false);

		if ($path === false)
		{
			throw new \app\Exception_NotApplicable('You must provide a path.');
		}

		// read composer.json
		$syspath = \app\Env::key('sys.path');

		if (\file_exists($syspath.'composer.json'))
		{
			$composerjson = \json_decode(\app\Filesystem::gets($syspath.'composer.json'), true);
			if (isset($composerjson['config'], $composerjson['config']['bin-dir']))
			{
				$phpunitcmd = \realpath($syspath.$composerjson['config']['bin-dir'].DIRECTORY_SEPARATOR.'phpunit');
			}
			else # default bin dir
			{
				$phpunitcmd = \realpath($syspath.'bin'.DIRECTORY_SEPARATOR.'phpunit');
			}
		}
		else # no composer file defined
		{
			// search for bin definition
			$bin = \app\CFS::config('mjolnir/bin');

			if ($bin['phpunit'])
			{
				$phpunitcmd = $bin['phpunit'];
			}
			else # fallback to default
			{
				$phpunitcmd = $syspath.'bin/phpunit';
			}
		}

		$etcpath = $this->etcpath();

		if ( ! \file_exists($etcpath.'mjolnir'.EXT))
		{
			throw new \app\Exception_NotApplicable('Unable to detect bootstrap file. Looked for ['.$etcpath.'mjolnir'.EXT.'].');
		}

		$tmppath = $this->tmppath();

		// we ensure the coverage name is legitimate
		$coveragename = \str_replace(' ', '-', $this->coveragename($consistent));

		$bootstrapfile = \str_replace('\\', '/', $etcpath.'mjolnir'.EXT);
		$coveragefile = \str_replace('\\', '/', $tmppath.$coveragename);

		$cmd = "--coverage-html={$coveragefile} --bootstrap={$bootstrapfile} {$path}";
		$clean_command = \str_replace(\str_replace('\\', '/', \app\Env::key('sys.path', '')), '', $cmd);

		if (\file_exists($coveragefile))
		{
			\app\Filesystem::delete($coveragefile);
		}

		$this->writer->writef("phpunit $clean_command")->eol()->eol();

		\passthru("$phpunitcmd $cmd");
	}

	/**
	 * @return string main configuration and environment store path
	 */
	protected function etcpath()
	{
		return \app\Env::key('etc.path');
	}

	/**
	 * @return string temporary file path to store reports
	 */
	protected function tmppath()
	{
		return \app\Env::key('tmp.path');
	}

	/**
	 * @return string html coverage directory name
	 */
	protected function coveragename($consistent)
	{
		if ($consistent)
		{
			return 'phpunit-coverage';
		}
		else # ensure unsued name
		{
			return 'phpunit-coverage-'.\time();
		}
	}

} # class
