<?php namespace mjolnir\cfs;

/**
 * @author  Ibidem Team
 * @version 1.0
 */
interface BenchmarkInterface
{
	/**
	 * Increment a counter.
	 */
	static function count($key, $increment = 1);

	/**
	 * Start benchmark for key. The benchmark MUST be stopped. If it is not
	 * stopped it is considered a logical error, and the benchmark will be
	 * marked as an error when calculating stats.
	 *
	 * @return mixed
	 */
	static function token($key, $category = null, $auto = false);

	/**
	 * Stop timer.
	 */
	static function stop($token);

	/**
	 * Stop timer and return time for debug purposes.
	 */
	static function debug($token);

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
	static function stats();

	/**
	 * @return string
	 */
	static function htmlstats($cutnamespaces = null);

} # interface
