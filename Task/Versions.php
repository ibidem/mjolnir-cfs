<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Versions extends \app\Task_Base
{
	/**
	 * @var array
	 */
	protected static $defaults = array
		(
			'name' => null,
			'major' => '0',
			'minor' => '0',
			'hotfix' => '0',
			'tag' => null,
		);

	/**
	 * ...
	 */
	function run()
	{
		$versions = \app\CFS::config('version');
		$name_length = 10; # average name length

		foreach (\array_keys($versions) as $key)
		{
			if ($name_length < \strlen($key))
			{
				$name_length = \strlen($key);
			}
		}

		$format = ' %'.$name_length.'s - %s';

		foreach ($versions as $key => $info)
		{
			$v = \app\CFS::merge(static::$defaults, $info);
			$version = $v['major'].'.'.$v['minor']
				. ($v['hotfix'] !== '0' ? '.'.$v['hotfix'] : '')
				. ($v['tag'] !== null ? '-'.$v['tag'] : '');

			$this->writer->writef($format, $key, $version)->eol();
		}
	}

} # class