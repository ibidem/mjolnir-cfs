<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Overlord extends \app\Instantiatable implements \mjolnir\types\TaskRunner
{
	use \app\Trait_TaskRunner;

	/**
	 * @var array
	 */
	protected static $helpcommands = array
		(
			'help', '--help', '-h', '-?', '-help'
		);

	/**
	 * @var array
	 */
	protected static $command_struct = array
		(
			'category' => 'etc',
			'description' => [],
			'flags' => [],
		);

	/**
	 * @var array
	 */
	protected static $flag_struct = array
		(
			'description' => 'Description not avaiable.',
			'default' => null,
			'type' => 'toggle',
			'short' => null,
		);

	/**
	 * @var string
	 */
	protected static $commandname = 'order';

	/**
	 * @var int
	 */
	protected $argc;

	/**
	 * @var array
	 */
	protected $argv;

	/**
	 * @param array args
	 */
	function args(array $args)
	{
		$this->argv = $args;
		$this->argc = \count($args);

		return $this;
	}

	/**
	 * @return string
	 */
	static function commandname()
	{
		return static::$commandname;
	}

	/**
	 * @param string command name
	 */
	static function commandname_is($commandname)
	{
		static::$commandname = $commandname;
	}

	/**
	 * Execute the layer.
	 */
	function run()
	{
		try
		{
			if ( ! $this->writer)
			{
				$this->writer = \app\Writer::instance();
			}

			\app\Task::consolewriter($this->writer);

			// ensure command line arguments
			if ( ! $this->argc || ! $this->argv)
			{
				$this->argc = $_SERVER['argc'];
				$this->argv = $_SERVER['argv'];
			}

			// ensure new line after command
			$this->writer->eol();

			// got paramters?
			if ($this->argc === 1)
			{
				$this->helptext(); # default to help on no command
				return;
			}

			// load configuration
			$tasks = \app\CFS::config('mjolnir/tasks');

			// get command
			$command = \strtolower($this->argv[1]);

			// help command? (handle internally)
			if (\in_array($command, static::$helpcommands))
			{
				if (isset($this->argv[2])) # specific help topic
				{
					$this->commandhelp($this->argv[2]);
					return;
				}
				else # general help
				{
					$this->helptext();
					return;
				}
			}
			else if (\in_array($command, ['-c', 'category']))
			{
				if (isset($this->argv[2])) # specific help topic
				{
					$this->helptext($this->argv[2]);
					return;
				}
				else # category not provided
				{
					$this->writer->printf('error', 'Please specify a category.')->eol();
					return;
				}
			}

			// valid command?
			if ( ! isset($tasks[$command]))
			{
				$this->writer->printf('error', 'Invalid command: '.$command)->eol();
				$this->writer->printf('status', 'Help', 'For more help type: php '.static::$commandname.' help')->eol();
				exit(1);
			}
			// check for help flag (-h or --help)
			for ($i = 2; $i < $this->argc; ++$i)
			{
				if ($this->argv[$i] === '--help' || $this->argv[$i] === '-h')
				{
					$this->commandhelp($command);
					return;
				}
			}
			// normalize command
			$tasks[$command] = static::normalize($tasks[$command], $command);
			// initialize configuration
			$config = array();
			foreach ($tasks[$command]['flags'] as $flag => $flaginfo)
			{
				$config[$flag] = $flaginfo['default'];
			}
			// check flags
			$flags = \array_keys($tasks[$command]['flags']);
			$task_flags = \app\CFS::config('mjolnir/task-flags');
			foreach ($flags as $flagkey)
			{
				if ( ! isset($tasks[$command]['flags'][$flagkey]))
				{
					$this->writer->printf('error', 'Invalid configuration');
				}

				$flag = $tasks[$command]['flags'][$flagkey];
				for ($i = 2; $i < $this->argc; ++$i)
				{
					if ($this->argv[$i] === '--'.$flagkey || $this->argv[$i] === '-'.$flag['short'])
					{
						if ($flag['type'] === 'toggle')
						{
							// toggle is a special flag type; always false by
							// default, switches to true on use
							$config[$flagkey] = true;
						}
						else # non-toggle type
						{
							$config[$flagkey] = \call_user_func($task_flags[$flag['type']], $i, $this->argv);
						}
					}
				}
			}

			// check for missing flags
			$missing_flags = array();
			foreach ($config as $flag => $value)
			{
				if ($value === null)
				{
					$missing_flags[] = $flag;
				}
			}

			// handle missing flags
			if ( ! empty($missing_flags))
			{
				$this->writer
					->printf('error', 'Missing required flags. Command terminated.')->eol()->eol()
					->printf('status', 'Help', 'For help type: '.static::$commandname.' '.$command.' -h')->eol()
					->eol()
					->printf('subtitle', 'Missing Flags');

				$this->render_flags($tasks[$command], $missing_flags);
				$this->writer->eol();
				exit(1);
			}

			// run the task

			// clean writer

			if ($this->writer == null)
			{
				$taskwriter = \app\Writer::instance()
					->stdout_is($this->writer->stdout())
					->stderr_is($this->writer->stderr());
			}
			else # no writer set
			{
				$taskwriter = $this->writer;
			}

			$task = \app\Task::invoke($command)
				->writer_is($taskwriter);

			foreach ($config as $key => $value)
			{
				$task->set($key, $value);
			}

			$task->run();

			// ensure new line after execution
			$this->writer->eol();
		}
		catch (\Exception $e)
		{
			$this->exception($e);
		}
	}

	/**
	 * Fills body and approprite calls for current layer, and passes the
	 * exception up to be processed by the layer above, if the layer has a
	 * parent.
	 *
	 * @param boolean layer is origin of exception?
	 */
	function exception(\Exception $exception)
	{
		\mjolnir\log_exception($exception);
		throw $exception;
	}

	/**
	 * General help information.
	 */
	function helptext($targetcategory = null)
	{
		$stdout = $this->writer;
		$stdout->printf('title', 'Overlord');
		$stdout->writef("    USAGE: ".static::$commandname." [command] [flags]")->eol();
		$stdout->writef("       eg. ".static::$commandname." example:cmd -i Joe --greeting \"Greetings, Mr.\" --date")->eol()->eol();
		$stdout->writef("     Help: ".static::$commandname." help")->eol();
		$stdout->writef("           ".static::$commandname." [command] -h")->eol();
		$stdout->writef("           ".static::$commandname." -c [category]")->eol();
		$stdout->eol()->eol();

		// load config
		$cli = \app\CFS::config('mjolnir/tasks');
		\ksort($cli);

		$taskorder = \app\CFS::config('mjolnir/task-categories');
		\uasort
			(
				$taskorder,
				function ($a, $b)
					{
						return $b['priority'] - $a['priority'];
					}
			);

		$orderedcategories = [];
		foreach ($taskorder as $category => $conf)
		{
			if ($targetcategory === null || \strtolower($category) == \strtolower($targetcategory))
			{
				$orderedcategories[$category] = [];
			}
		}
		$orderedcategories['unsorted'] = [];

		// normalize
		foreach ($cli as $command => $commandinfo)
		{
			if ($targetcategory !== null && \strtolower($commandinfo['category']) != \strtolower($targetcategory))
			{
				continue;
			}

			if (isset($commandinfo['category'], $orderedcategories[$commandinfo['category']]))
			{
				$orderedcategories[$commandinfo['category']][$command] = static::normalize($commandinfo, $command);
			}
			else # unrecognized category
			{
				$orderedcategories['unsorted'][$command] = static::normalize($commandinfo, $command);
			}
		}

		$cli = [];
		foreach ($orderedcategories as $category => $tasks)
		{
			if ($targetcategory !== null && \strtolower($category) != \strtolower($targetcategory))
			{
				continue;
			}

			foreach ($tasks as $task => $taskinfo)
			{
				$cli[$task] = $taskinfo;
			}
		}

//		// sort commands
//		\uasort
//			(
//				$cli,
//				function ($a, $b)
//				{
//					return \strcmp($a['category'].$a['commandname'], $b['category'].$b['commandname']);
//				}
//			);

		// determine max length
		$max_command_length = 4;
		foreach (\array_keys($cli) as $command)
		{
			if (\strlen($command) > $max_command_length)
			{
				$max_command_length = \strlen($command);
			}
		}
		$command_format = '  %-'.$max_command_length.'s  - ';

		$category = null;
		// configuration commands
		foreach ($cli as $command => $info)
		{
			if ($category !== $info['category'])
			{
				$category === null or $stdout->eol();
				$stdout->printf('subtitle', $info['category']);
				$category = $info['category'];
			}

			$stdout
				->printf
					(
						'list',
						\sprintf($command_format, $command),
						$info['description'][0],
						$max_command_length + 6
					)
				->eol();
		}

		// terminate after displaying help
		$stdout->eol();
	}

	/**
	 * @param string command
	 */
	protected function commandhelp($commandname)
	{
		$stdout = $this->writer;
		$stdout->printf('title', 'Help for '.$commandname);
		// configuration
		$cli = \app\CFS::config('mjolnir/tasks');

		// normalize
		$command = static::normalize($cli[$commandname], $commandname);

		// display quick syntax help
		$helptext_head = ' '.static::$commandname.' '.$commandname;
		$helptext_head_length = \strlen($helptext_head) + 1;
		$helptext = '';
		foreach ($command['flags'] as $flag => $flaginfo)
		{
			if ($flaginfo['default'] === null) # mandatory paramter
			{
				$helptext .= ' --'.$flag;
				if ($command['flags'][$flag]['type'] !== 'toggle')
				{
					// the & is a placeholder for space to symbolize we don't
					// want a break; see ->listwrite later
					$helptext .= '&<'.$command['flags'][$flag]['type'].'>';
				}
			}
			else # optional paramter
			{
				$helptext .= ' [--'.$flag;
				if ($command['flags'][$flag]['type'] !== 'toggle')
				{
					$helptext .= '&<'.$command['flags'][$flag]['type'].'>';
				}
				$helptext .= ']';
			}
		}

		$stdout
			->printf('list', $helptext_head, $helptext, $helptext_head_length, '&')
			->eol()->eol();

		// display description
		foreach ($command['description'] as $description)
		{
			$stdout
				->printf('wrap', $description, 4)
				->eol()->eol();
		}
		// display detailed flag information
		$stdout->eol()->printf('subtitle', 'Flags');
		if (\count($command['flags']) === 0)
		{
			$stdout->printf('wrap', "    This command does not accept any flags.");
		}
		else # has flags
		{
			$stdout->printf('wrap', ' '.static::$commandname.' '.$commandname)->eol();
			$this->render_flags($command, null);
			$stdout->eol();
			$stdout->eol()->printf('subtitle', 'Default Values');
			$count = $this->render_flags($command, null, 'default');
			if (empty($count))
			{
				$stdout->printf('wrap', '    All flags are required.');
			}
		}

		// terminate after displaying help
		$stdout->eol();
	}

	/**
	 * Gurantees the default structure is set for the command and it's flags.
	 *
	 * @param array command
	 * @return array
	 */
	protected static function normalize($command, $commandname)
	{
		$command = \app\CFS::merge(static::$command_struct, $command);

		$command['commandname'] = $commandname;

		if (empty($command['description']))
		{
			$command['description'] = ['No description available at this time.'];
		}
		$normalizedflags = array();
		foreach ($command['flags'] as $flag => $flaginfo)
		{
			$normalizedflags[$flag] = \app\CFS::merge(static::$flag_struct, $flaginfo);
			// gurantee toggles are boolean
			if ($normalizedflags[$flag]['type'] === 'toggle' && $normalizedflags[$flag]['default'] === null)
			{
				$normalizedflags[$flag]['default'] = false;
			}
		}

		// re-arranging; sort functions would achive the same result but may not
		// maintain the configuration order -- which may help in understanding
		// the command's flags
		$sortedflags = array();
		// required flags
		foreach ($normalizedflags as $key => $flag)
		{
			if ($flag['type'] !== 'toggle' && $flag['default'] === null)
			{
				$sortedflags[$key] = $flag;
			}
		}
		// optional flags
		foreach ($normalizedflags as $key => $flag)
		{
			if ($flag['type'] !== 'toggle' && $flag['default'] !== null)
			{
				$sortedflags[$key] = $flag;
			}
		}
		// toggle's (automatically optional flags)
		foreach ($normalizedflags as $key => $flag)
		{
			if ($flag['type'] === 'toggle')
			{
				$sortedflags[$key] = $flag;
			}
		}
		$command['flags'] = $sortedflags;

		return $command;
	}

	/**
	 * @param array command
	 * @param array flagkeys
	 * @param string description key
	 * @return int
	 */
	protected function render_flags($command, $flagkeys = null,	$descriptionkey = 'description')
	{
		if ($flagkeys === null)
		{
			$flagkeys = \array_keys($command['flags']);
		}

		// detect maximum flag length
		$max_flag_length = 0;
		foreach ($flagkeys as $flag)
		{
			$length = \strlen($flag);
			if ($command['flags'][$flag]['type'] !== 'toggle')
			{
				$clean_type = $command['flags'][$flag]['type'];
				$length += \strlen($clean_type) + 5;
			}
			if ($length > $max_flag_length)
			{
				$max_flag_length = $length;
			}
		}
		$displaycount = 0;
		$format_dt = ' %4s %-'.($max_flag_length+1).'s  - ';
		foreach ($flagkeys as $flag)
		{
			$flaginfo = $command['flags'][$flag];
			// only display flags with description data
			if ($flaginfo[$descriptionkey] !== null)
			{
				$clean_type = $flaginfo['type'];
				$type = $clean_type === 'toggle' ? '' : '<'.$clean_type.'>';
				$short = $flaginfo['short'] === null ? '' : '-'.$flaginfo['short'];
				if (\is_bool($flaginfo[$descriptionkey]))
				{
					$description = $flaginfo[$descriptionkey] ? 'on' : 'off';
				}
				else if (\is_string($flaginfo[$descriptionkey]) && empty($flaginfo[$descriptionkey]))
				{
					$description = '(empty string)';
				}
				else # normal value
				{
					$description = $flaginfo[$descriptionkey];
				}

				$this->writer
					->printf
						(
							'list',
							\sprintf($format_dt, $short, '--'.$flag.' '.$type),
							$description,
							$max_flag_length + 10
						)
					->eol();

				++$displaycount;
			}
		}

		return $displaycount;
	}

} # class
