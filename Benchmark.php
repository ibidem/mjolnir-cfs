<?php namespace mjolnir\cfs;

if ( ! \interface_exists('\mjolnir\cfs\BenchmarkInterface', false))
{
	require 'BenchmarkInterface'.EXT;
}

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Benchmark implements BenchmarkInterface
{
	#
	# Intentionally not using real_usage for \memory_get_usage
	#

	/**
	 * @var array enum
	 */
	protected static $states = array
		(
			'nominal'   => 0,
			'corrupt'   => 1,
			'unstopped' => 2,
			'multistop' => 3,
		);

	/**
	 * @var array
	 */
	protected static $counters = [];

	/**
	 * @var array
	 */
	protected static $tokens = [];

	/**
	 * Increment a counter.
	 */
	static function count($key, $increment = 1)
	{
		static::$counters[$key] = isset(static::$counters[$key]) ? static::$counters[$key] + $increment : $increment;
	}

	/**
	 * Start benchmark for key. The benchmark MUST be stopped. If it is not
	 * stopped it is considered a logical error and the benchmark will be
	 * marked as an error when calculating stats. Unless auto is true, then the
	 * benchmark will be auto stopped on stat request.
	 *
	 * @return string token
	 */
	static function token($key, $category = null, $auto = false)
	{
		static $counter = 0;

		$token = $counter++;

		static::$tokens[$token] = array
			(
				$auto,
				$key,
				$category,
				\microtime(true),
				\memory_get_usage()
			);

		return $token;
	}

	/**
	 * Stop timer.
	 */
	static function stop($token)
	{
		static::$tokens[$token][0] = false;

		// Stop the benchmark
		static::$tokens[$token][] = \microtime(true);
		static::$tokens[$token][] = \memory_get_usage();
	}

	/**
	 * Format:
	 *
	 *	[
	 *		key => [
	 *			category,
	 *			status,
	 *			errors,
	 *			count,
	 *			min => [ time, memory ],
	 *			max => [ time, memory ],
	 *			avg => [ time, memory ],
	 *			total => [ time, memory ]
	 *		],
	 *	]
	 *
	 * @return array
	 */
	static function stats()
	{
		$template = array
			(
				'category' => null,
				'auto' => false,
				'count' => 0,  # errors excluded
				'errors' => 0, # number of entries with errors
				'state' => static::$states['nominal'],
				'min'   => [ 'time' => null, 'memory' => null ],
				'max'   => [ 'time' => null, 'memory' => null ],
				'avg'   => [ 'time' => null, 'memory' => null ],
				'total' => [ 'time' => 0,    'memory' => 0    ],
			);

		$stats = [];

		foreach (static::$tokens as $token => &$info)
		{
			// integrate template
			isset($stats[$info[1]]) or $stats[$info[1]] = $template;

			$entry = &$stats[$info[1]];

			if ($info[0])
			{
				static::stop($token);
				$entry['auto'] = true;
			}

			$entry['category'] = $info[2];

			// check for false positives
			if (\count($info) < 7)
			{
				$entry['state'] = static::$states['unstopped'];
				++$entry['errors'];
			}
			else if (\count($info) > 7)
			{
				$entry['state'] = static::$states['multistop'];
				++$entry['errors'];
			}
			else if ($info[5] < $info[3] || $info[6] < $info[4])
			{
				$entry['state'] = static::$states['corrupt'];
				++$entry['errors'];
			}
			else # no errors
			{
				++$entry['count'];

				$time = $info[5] - $info[3];
				$memory = $info[6] - $info[4];

				$entry['total']['time'] += $time;
				$entry['total']['memory'] += $memory;

				if ($entry['min']['time'] === null || $entry['min']['time'] > $time)
				{
					$entry['min']['time'] = $time;
				}

				if ($entry['max']['time'] === null || $entry['max']['time'] < $time)
				{
					$entry['max']['time'] = $time;
				}

				if ($entry['min']['memory'] === null || $entry['min']['memory'] > $memory)
				{
					$entry['min']['memory'] = $memory;
				}

				if ($entry['max']['memory'] === null || $entry['max']['memory'] < $memory)
				{
					$entry['max']['memory'] = $memory;
				}
			}
		}

		// calculate averages
		foreach ($stats as &$info)
		{
			if ($info['state'] === static::$states['nominal'] && $info['count'] > 0)
			{
				if ($info['count'] != 1)
				{
					$info['avg']['time'] = $info['total']['time'] / $info['count'];
					$info['avg']['memory'] = $info['total']['memory'] / $info['count'];
				}
				else # single
				{
					$info['avg']['time'] = $info['max']['time'];
					$info['avg']['memory'] = $info['max']['memory'];
				}
			}
		}

		\uasort
		(
			$stats,
			function ($a, $b)
			{
				return \strcmp($a['category'], $b['category']);
			}
		);

		return $stats;
	}

	/**
	 * @return string html
	 */
	static function htmlstats($cutnamespaces = null)
	{
		$cutnamespaces !== null or $cutnamespaces = true;

		$html = '<br><table style="text-align: right; font-family: monospace; background: #eee; color: #222; width: 100%">';
		$html .= '<thead>';
		$html .= '<tr><th>Count</th><th style="text-align: left; padding-left: 10px">Benchmark</th><th>min</th><th>max</th><th>avg</th><th>total</th><th>error</th></tr>';
		$html .= '</thead>';

		$category = false;
		$state = \array_flip(static::$states);
		foreach (static::stats() as $key => $entry)
		{
			if ($entry['category'] !== $category)
			{
				$category = $entry['category'];
				$html .= '<tbody>';

				if ($entry['category'] !== null)
				{
					$categoryname = $category;
				}
				else # null, as in unsorted
				{
					$categoryname = 'unsorted';
				}

				$html .= "<tr><th colspan='7' style='text-align: left; padding: 3px 10px; background: #222; color: #eee'><big>$category</big></th></tr>";
			}

			$html .= '<tr>';
			$html .= '<td rowspan="2" style=""><big>'.$entry['count'].'</big></td>';
			$html .= '<td rowspan="2" style="text-align: left; padding: 2px 10px"><big>'.($cutnamespaces ? \preg_replace('#(^| )[a-z0-9_\\\]*\\\#', '', $key) : $key).'</big></td>';
			$html .= '<td>'.\number_format($entry['min']['time'], 9).'&nbsp;sec</td>';
			$html .= '<td>'.\number_format($entry['max']['time'], 9).'&nbsp;sec</td>';
			$html .= '<td>'.\number_format($entry['avg']['time'], 9).'&nbsp;sec</td>';
			$html .= '<td>'.\number_format($entry['total']['time'], 9).'&nbsp;sec</td>';
			$html .= '<td title="'.$state[$entry['state']].'">'.\number_format(($entry['count'] + $entry['errors']) * $entry['errors'] / 100, 2).'&nbsp;%</td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td>'.static::htmlbytes($entry['min']['memory']).'</td>';
			$html .= '<td>'.static::htmlbytes($entry['max']['memory']).'</td>';
			$html .= '<td>'.static::htmlbytes($entry['avg']['memory']).'</td>';
			$html .= '<td></td>';
			$html .= '<td></td>';
			$html .= '</tr>';

		}

		if ($category !== false)
		{
			$html .= '</tbody>';
		}

		// compute counters
		$counthtml = 'PHP peak usage '.\str_replace(' ', '&nbsp;', \app\Text::prettybytes(\memory_get_peak_usage(), 0));
		foreach (static::$counters as $counter => $count)
		{
			if ( ! $cutnamespaces)
			{
				$counthtml .= " &nbsp;&nbsp; $counter $count";
			}
			else # cut namespaces
			{
				$counthtml .= " &nbsp;&nbsp; ".\preg_replace('#^.*\\\#', '', $key)." $count";
			}

		}

		$html .= "<tr><td colspan='7' style='padding: 10px; background: #777; color: #fff; text-align: left;'>$counthtml</td></tr>";

		$html .= '</table>';

		return $html;
	}

	/**
	 * @return string
	 */
	protected static function htmlbytes($bytes)
	{
		if ($bytes < 1024)
		{
			return \str_replace(' B', '&nbsp;&nbsp;&nbsp;B', \app\Text::prettybytes($bytes, 0));
		}
		else # KiB or higher
		{
			return \str_replace(' ', '&nbsp;', \app\Text::prettybytes($bytes, 0));
		}
	}

} # class

