<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Config extends \app\Task_Base
{
	/**
	 * Execute task.
	 */
	function run()
	{
		$config = $this->get('config', null);
		
		if ($config != null)
		{
			$config = \app\CFS::config($config); 
		}
		
		\print_r($config);
	}

} # class
