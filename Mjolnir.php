<?php namespace mjolnir\cfs;

/**
 * The purpose of this class is to store some of the grunt work that would
 * otherwise go into public files, etc.
 *
 * @package    mjolnir
 * @category   Base
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Mjolnir
{
	/**
	 * Searches for setup file and loads it.
	 *
	 * The purpose of this is to allow for the framework to auto-bootstrap in
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
			if (\file_exists($dir.'mjolnir'.EXT))
			{
				require_once $dir.'mjolnir'.EXT;
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
	static function www($system_config)
	{
		if (PHP_VERSION_ID < 50404)
		{
			die(' PHP version 5.4.4 or greater required.');
		}

		// downtime?
		if ($system_config['maintanence']['enabled'] && ( ! isset($_GET['passcode']) || $_GET['passcode'] !== $system_config['maintanence']['passcode']))
		{
			require 'downtime.php';
			exit;
		}

		// set language
		\app\Lang::lang($system_config['lang']);

		// check all routes
		\app\Route::check_all();

		// go though all relays
		\app\Relay::check_all();

		// do we have a default theme?
		if (\app\CFS::config('mjolnir/themes')['theme.default'] !== null)
		{
			try
			{
				\app\Layer::stack
					(
						\app\Layer_HTTP::instance(),
						\app\Layer_HTML::instance(),
						\app\Layer_Sandbox::instance()
							->caller
							(
								function ()
								{
									\app\ThemeView::instance()
										->errortarget('NotFound');

									throw new \app\Exception_NotFound
										(
											'The page you are trying to access doesn\'t appear to exist. Sorry!'
										);
								}
							)
					);
			}
			catch (\Exception $e)
			{
				// do nothing
			}
		}
		else if (\file_exists(PUBDIR.'404'.EXT))
		{
			require PUBDIR.'404'.EXT;
		}
		else # no 404 file
		{
			\header("HTTP/1.0 404 Not Found");
			echo '404 - Not Found';
		}

		exit(1);
	}

	/**
	 * Shorthand.
	 *
	 * Runs standard theme procedures.
	 */
	static function themes($system_config)
	{
		$stack = function ($relay, $target)
			{
				\app\Layer::stack
					(
						\app\Layer_HTTP::instance(),
						\app\Layer_Theme::instance()
							->relay_config($relay)
					);
			};

		\app\Relay::process('\mjolnir\theme\Layer_Theme::complete-style', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::style', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::style-src', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::js-bootstrap', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::complete-script-map', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::script-map', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::script-src', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::complete-script', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::script', $stack);
		\app\Relay::process('\mjolnir\theme\Layer_Theme::resource', $stack);

		// we failed relays
		\header("HTTP/1.0 404 Not Found");
		echo '404 - Media Not Found';
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
			\app\Layer::stack
				(
					\app\Layer_TaskRunner::instance()
						->writer(\app\Writer_Console::instance())
						->args($_SERVER['argv'])
				);
		}
	}

} # class