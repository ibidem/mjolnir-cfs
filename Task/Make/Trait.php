<?php namespace mjolnir\cfs;

/**
 * Syntactic sugar task.
 *
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Make_Trait extends \app\Task_Make_Class
{
	/**
	 * @var string
	 */
	protected static $filetype = 'trait';

	/**
	 * ...
	 */
	function run()
	{
		$this->set('class', $this->get('trait', false));
		$this->set('with-tests', false);
		$this->set('category', false);
		$this->set('library', true);

		parent::run();
	}

} # class
