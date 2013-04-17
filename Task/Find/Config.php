<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Find_Config extends \app\Task_Base
{
	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task_Find_File::instance()
			->writer_is($this->writer())
			->metadata_is($this->metadata())
			->set('path', 'config/'.$this->get('config'))
			->run();
	}

} # class
