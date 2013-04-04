<?php namespace mjolnir\cfs;

/**
 * The purpose of this class is to store some of the grunt work that would
 * otherwise go into public files, etc.
 *
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Mjolnir
{
	/**
	 * Searches for setup file and loads it.
	 *
	 * The purpose of this is to allow for the library to auto-bootstrap in
	 * (shitty) composer environments that do not support basic concepts such as
	 * autoloaders.
	 */
	static function init()
	{
		// the current directory is <vendor>/mjolnir/base/ where vendor is
		// whatever the project composer configuration is setup to store
		// packages in, so...
		$vendor_root = \realpath(\realpath(__DIR__).'/../..').'/';

		if ( ! \defined('EXT'))
		{
			\define('EXT', '.php');
		}

		$dir = \realpath($vendor_root.'/..').'/';

		do
		{
			if (\file_exists($dir.'etc/mjolnir'.EXT))
			{
				require_once $dir.'etc/mjolnir'.EXT;
				return;
			}

			$dir = \realpath($dir.'/..').'/';
		}
		while ($dir !== false);

		echo PHP_EOL.' Mjolnir: Your composer vendor directory structure can not be interpreted.'.PHP_EOL;
	}

	/**
	 * Behat behaviour.
	 */
	static function behat()
	{
		if ( ! \defined('IS_UNITTEST'))
		{
			\define('IS_UNITTEST', true);
		}

		// bootstrap
		static::init();

		// load assertion helpers
		require_once \app\CFS::dir('functions/mjolnir/').'assertions'.EXT;
	}

	/**
	 * Shorthand.
	 *
	 * Runs standard http procedures.
	 */
	static function www($wwwconfig, $wwwpath)
	{
		if (PHP_VERSION_ID < 50410)
		{
			die(' PHP version 5.4.10 or greater required (except 5.4.11).');
		}

		if (PHP_VERSION_ID == 50411)
		{
			die(' PHP version 5.4.11 not supported.');
		}

		// downtime?
		if ($wwwconfig['maintenance']['enabled'] && ( ! isset($_GET['passcode']) || $_GET['passcode'] !== $wwwconfig['maintanence']['passcode']))
		{
			require 'downtime.php';
			exit;
		}

		\app\Env::ensure('www.config', $wwwconfig);

		// set language
		\app\Lang::targetlang_is($wwwconfig['lang']);

		// check all routes
		\app\Router::check_all_routes();

		// go though all relays
		\app\Router::check_all_relays();

		\mjolnir\log('404', 'Visitor arrived at "'.$_SERVER['REQUEST_URI'].'" and encountered 404.');

		// do we have a default theme?
		if (\app\CFS::config('mjolnir/themes')['theme.default'] !== null)
		{

			try
			{
				$relaynode = \app\RelayNode::instance
					(
						[
							'matcher' => true,
							'controller' => '\app\Controller_Error',
							'default.action' => 'process_error',
							'action' => null,
							'prefix' => '',
						]
					);

				$channel = \app\Channel::instance()
					->set('relaynode', $relaynode)
					->set('exception', new \app\Exception_NotFound('The page "'.\app\Server::request_uri().'" ('.\app\Server::request_method().') doesn\'t exist on the server.'));

				echo \app\Application::stack
					(
						\app\Layer_HTTP::instance(),
						\app\Layer_HTML::instance(),
						\app\Layer_Theme::instance(),
						\app\Layer_MVC::instance()
					)
					->channel_is($channel)
					->render();

				exit(1);
			}
			catch (\Exception $exception)
			{
				\mjolnir\log_exception($exception);
			}
		}

		// fallback; in case above fails
		if (\file_exists(\app\Env::key('www.path').'404'.EXT))
		{
			require \app\Env::key('www.path').'404'.EXT;
		}
		else # no 404 file
		{
			\header("HTTP/1.0 404 Not Found");
			echo 'Not Found';
		}

		exit(1);
	}

	/**
	 * Theme resources.
	 */
	static function resource($wwwconfig, $wwwpath)
	{
		\app\Benchmark::token(__METHOD__, 'Application', true);

		$theme = \app\CFS::config('mjolnir/layer-stacks')['resource'];
		$drivers = \app\CFS::config('mjolnir/theme-drivers')['drivers'];
		$url = \app\Server::request_uri();
		$priority = \app\CFS::config('mjolnir/theme-drivers')['priority'];

		\asort($priority);
		
		$processing = [];
		foreach ($priority as $key => $info)
		{
			$processing[$key] = [];
		}

		foreach ($drivers as $driver => $config)
		{
			if ($config['enabled'])
			{
				isset($config['type']) or $config['type'] = 'default';
				$processing[$config['type']][] = function () use ($driver, $theme, $url)
					{
						\app\Router::process("mjolnir:theme/themedriver/$driver.route", $theme, null, $url);
					};
			}
		}

		foreach ($processing as $key => $processes)
		{
			foreach ($processes as $relay)
			{
				$relay();
			}
		}

		// we failed relays
		\header("HTTP/1.0 404 Not Found");
		exit(1);
	}

	/**
	 * Shorthand.
	 *
	 * Runs standard command line utility.
	 */
	static function overlord()
	{
		// running on a the command line?
		if (\php_sapi_name() === 'cli')
		{
			\app\Overlord::instance()
				->args($_SERVER['argv'])
				->run();
		}
	}

} # class
