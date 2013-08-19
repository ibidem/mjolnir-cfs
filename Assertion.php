<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Assertion extends \app\Instantiatable
{
	/**
	 * @var mixed
	 */
	protected $asserted;

	/**
	 * @return static
	 */
	static function of($asserted)
	{
		$i = static::instance();
		$i->asserted = $asserted;

		return $i;
	}

	/**
	 * Strict equality check.
	 */
	function equals($expected)
	{
		if (\is_array($expected))
		{
			if (\is_array($this->asserted))
			{
				if (\serialize($expected) !== \serialize($this->asserted))
				{
					$expected_value = $this->valuedef($expected);
					$asserted_value = $this->valuedef($this->asserted);
					throw new \app\Exception("Expected {$expected_value}, recieved {$asserted_value}");
				}
			}
			else # non-array
			{
				$asserted_value = $this->valuedef($this->asserted);
				throw new \app\Exception("Expected Array, recieved {$asserted_value}");
			}
		}
		else if ($expected !== $this->asserted)
		{
			$expected_value = $this->valuedef($expected);
			$asserted_value = $this->valuedef($this->asserted);
			throw new \app\Exception("Expected {$expected_value}, recieved {$asserted_value}");
		}
	}

	/**
	 * Same as equals, only loose type checks.
	 */
	function loosly_equals($expected)
	{
		if (\is_array($expected))
		{
			return $this->equals($expected);
		}
		else if ($expected != $this->asserted)
		{
			$expected_value = $this->valuedef($expected);
			$asserted_value = $this->valuedef($this->asserted);
			throw new \app\Exception("Expected {$expected_value}, recieved {$asserted_value}");
		}
	}

	/**
	 * @return string normalized value definition
	 */
	protected function valuedef($value)
	{
		$type = \gettype($value);
		if ($type === 'NULL')
		{
			return '<NULL>';
		}
		else if ($type == 'string')
		{
			return '"'.$value.'"';
		}
		else if ($type == 'boolean')
		{
			return '<'.($value ? 'TRUE' : 'FALSE').'>';
		}
		else if ($type == 'array')
		{
			return '<Array['.\serialize($value).']>';
		}
		else # other
		{
			return "<{$type}[$value]>";
		}
	}

} # class
