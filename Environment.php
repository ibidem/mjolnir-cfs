<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Environment extends \app\Instantiatable
{
	/**
	 * @var array
	 */
	static protected $environments = [];

	/**
	 * @var string
	 */
	static protected $default_envname = 'main';

	/**
	 * @var array
	 */
	protected $keys = [];

	/**
	 * @return
	 */
	static function instance($envname = null)
	{
		$envname !== null or $envname = static::$default_envname;

		if ( ! isset(static::$environments[$envname]))
		{
			$instance = parent::instance();
			static::$environments[$envname] = $instance;
		}

		return static::$environments[$envname];
	}

	/**
	 * @return mixed
	 */
	function key($key, $default = null)
	{
		if (isset($this->keys[$key]))
		{
			return $this->keys[$key];
		}
		else # default
		{
			return $default;
		}
	}

	/**
	 * @return mixed
	 */
	function ensure($key, $value)
	{
		if ( ! isset($this->keys[$key]))
		{
			$this->keys[$key] = $value;
		}

		return $this->keys[$key];
	}

	/**
	 * @return static $this
	 */
	function set($key, $value)
	{
		$this->keys[$key] = $value;
		return $this;
	}

} # class
