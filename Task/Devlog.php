<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Devlog extends \app\Task_Base
{
	/**
	 * Execute task.
	 */
	function run()
	{
		$logpath = \app\Env::key('etc.path').'logs/short.log';
		\file_put_contents($logpath, '');
		\passthru("tail -f $logpath");
	}

} # class
