<?php namespace mjolnir\cfs;

use Symfony\Component\DependencyInjection\ContainerBuilder,
	Symfony\Component\Console\Input\ArrayInput,
	Symfony\Component\Console\Output\NullOutput;

use Behat\Behat\DependencyInjection\BehatExtension,
    Behat\Behat\DependencyInjection\Configuration\Loader;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Behat extends \app\Task_Base
{
	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$feature = $this->get('feature', false);

		$behat_flags = ' ';

		if ($_SERVER['argc'] > 2)
		{
			$args = $_SERVER['argv'];
			\array_shift($args);
			\array_shift($args);

			if ($feature !== false)
			{
				\array_shift($args);
				\array_shift($args);
			}

			$behat_flags .= \app\Arr::implode(' ', $args, function ($i, $v) {
				// is this a flag?
				if ( ! \preg_match('#^-[a-z-]+$#', $v))
				{
					// assume "literal value" and pass on as such
					return '\''.\addcslashes($v, '\'\\').'\'';
				}
				else # it's a flag; probably
				{
					return $v;
				}
			});
		}

		// verify behat is present
		$composer_config = \json_decode(\file_get_contents(\app\Env::key('sys.path').'composer.json'), true);
		$bindir = \trim($composer_config['config']['bin-dir'], '/');
		$behat_cmd = \app\Env::key('sys.path').$bindir.'/behat';
		if ( ! \file_exists($behat_cmd))
		{
			$this->writer->printf('status', 'Error', 'Missing behat runner.')->eol();
			$this->writer->printf('status', 'Help', 'Please verify you have behat in your composer file.')->eol();
			exit(1);
		}

		$paths = \app\CFS::paths();

		$filter = new \PHP_CodeCoverage_Filter();
		$filter->addDirectoryToBlacklist(\app\Env::key('sys.path'));

		$behat_commands = [];

		foreach ($paths as $path)
		{
			$dir_iterator = new \RecursiveDirectoryIterator($path);

			$iterator = new \RecursiveIteratorIterator
				(
					$dir_iterator,
					\RecursiveIteratorIterator::SELF_FIRST
				);

			foreach ($iterator as $file)
			{
				if (\preg_match('#behat.yaml$#', $file))
				{
					$pretty_location = \str_replace
						(
							DIRECTORY_SEPARATOR
								. \mjolnir\cfs\CFSInterface::APPDIR
								. DIRECTORY_SEPARATOR
							,
							'',
							\str_replace(\app\Env::key('sys.path'), '', $path)
						);

					$the_feature = \preg_replace('#^.*[/\\\]features[/\\\]#', '', \dirname($file));

					if ($feature !== false && ! \preg_match('#'.\strtolower($feature).'#', \strtolower($the_feature)))
					{
						continue;
					}

					// add relevant to filter
					$filter->addDirectoryToWhitelist(\realpath($path.'../').DIRECTORY_SEPARATOR);

					//$modulepath = \realpath($path.'../');
					//$filter->addFileToWhitelist($modulepath);
					//$filter->addDirectoryToBlacklist($modulepath.\mjolnir\cfs\CFSInterface::APPDIR);
					//$filter->addDirectoryToWhitelist($path.'features');
					//$filter->addDirectoryToWhitelist($path.'functions');

					$executed_cmd = $behat_cmd.' '.$behat_flags.' --config="'.$file.'"';

					$behat_commands[] = array
						(
							'title' => \str_repeat('-', 79).PHP_EOL
									.  'Processing feature "'.$the_feature.'" for '.$pretty_location.PHP_EOL
									.  \str_repeat('-', 79).PHP_EOL.PHP_EOL,
							'exec' => $executed_cmd,
							'file' => $file
						);
				}
			}
		}

		$coverage = new \PHP_CodeCoverage(null, $filter);

		$this->code_coverage($coverage, $behat_commands);

		$coverage_file_prefix = \app\Env::key('tmp.path')."code-coverage-".\microtime(true);
		$coverage_file_prefix_pretty = \str_replace(\str_replace('\\', '/', \app\Env::key('sys.path')), '', \str_replace('\\', '/', $coverage_file_prefix));

		$this->writer->eol()->writef('Generating Code Coverage ['.$coverage_file_prefix_pretty.'.cov]');
		$writer = new \PHP_CodeCoverage_Report_PHP;
		$writer->process($coverage, $coverage_file_prefix.'.cov');

		$this->writer->eol()->writef('Generating Code Coverage HTML ['.$coverage_file_prefix_pretty.']');
		$writer = new \PHP_CodeCoverage_Report_HTML;
		$writer->process($coverage, $coverage_file_prefix);
	}

	/**
	 * Code Coverage. Isolating as much as possible to avoid polution.
	 */
	protected function code_coverage($coverage, $commands)
	{
		\chdir(\app\Env::key('sys.path'));
		$coverage->start('Mjolnir Behat Test');

		foreach ($commands as $command)
		{
			echo PHP_EOL.$command['title'];

			$file = $command['file'];
			$container = new ContainerBuilder();

			$loader  = new Loader($file);
			$configs = $loader->loadConfiguration('default');

			$basePath = \realpath(\dirname($file));

			$extension = new BehatExtension($basePath);
			$extension->load($configs, $container);
			$container->addObjectResource($extension);

			$container->compile();


			$input = new ArrayInput
				(
					[
						'--ansi' => true
					]
				);

			$container->get('behat.console.command')->run($input, new NullOutput());
		}

		$coverage->stop();
	}

} # class
