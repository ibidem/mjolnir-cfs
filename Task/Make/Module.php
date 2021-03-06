<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Make_Module extends \app\Task_Base
{
	/**
	 * @return string version file contents
	 */
	protected function version_file($name)
	{
		return
			  '<?php return array'.PHP_EOL
			. "\t(".PHP_EOL
			. "\t\t'$name' => array".PHP_EOL
			. "\t\t\t(".PHP_EOL
			. "\t\t\t\t'major' => '1',".PHP_EOL
			. "\t\t\t\t'minor' => '0',".PHP_EOL
			. "\t\t\t\t'tag'   => 'liquid',".PHP_EOL
			. "\t\t\t)".PHP_EOL
			. "\t);".PHP_EOL
			;
	}

	/**
	 * @return string honeypot
	 */
	protected function honeypot_file($namespace)
	{
		return
			  '<?php namespace app;'.PHP_EOL
			. PHP_EOL
			. '// This is an IDE honeypot. It tells IDEs the class hirarchy, but otherwise has'.PHP_EOL
			. '// no effect on your application. :)'.PHP_EOL
			. PHP_EOL
			. '// HowTo: order honeypot -n \''.$namespace.'\''
			. PHP_EOL
			;
	}

	protected function access_file()
	{
		return
			  '<?php return array'.PHP_EOL
			. "\t(".PHP_EOL
			. "\t\t'aliaslist' => array".PHP_EOL
			. "\t\t\t(".PHP_EOL
			. "\t\t\t\t// \app\Auth::Guest => [ '+admin', '+mockup' ],".PHP_EOL
			. "\t\t\t)".PHP_EOL
			. "\t);"
			. PHP_EOL
			;
	}

	protected function mockup_relays_file()
	{
		return
			  '<?php return array'.PHP_EOL
			. "\t(".PHP_EOL
			. "\t\t".'\'\mjolnir\theme\mockup\'        => [ \'enabled\' => true ],'.PHP_EOL
			. "\t\t".'\'\mjolnir\theme\mockup-errors\' => [ \'enabled\' => true ],'.PHP_EOL
			. "\t\t".'\'\mjolnir\theme\mockup-form\'   => [ \'enabled\' => true ],'.PHP_EOL
			. "\t);"
			. PHP_EOL
			;
	}

	protected function sandbox_relays_file()
	{
		return
			  '<?php return array'.PHP_EOL
			. "\t(".PHP_EOL
			. "\t\t".'\'\mjolnir\sandbox\' => [ \'enabled\' => true ],'.PHP_EOL
			. "\t);"
			. PHP_EOL
			;
	}

	protected function sandbox_file()
	{
		return
			  '<?php namespace mjolnir\sandbox;'.PHP_EOL
			. PHP_EOL
			. 'function tester()'.PHP_EOL
			. '{'.PHP_EOL
			. "\techo 'hello, sandbox';".PHP_EOL
			. '}'.PHP_EOL
			. PHP_EOL
			. "\app\Sandbox::process('\\mjolnir\\sandbox\\tester');".PHP_EOL
			. PHP_EOL
			;
	}

	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);

		$name = $this->get('name', false);
		$namespace = $this->get('namespace', '');
		$forced = $this->get('forced', false);
		$mockup_template = $this->get('mockup-template', false);
		$sandbox_template = $this->get('sandbox-template', false);

		// module exists?
		$module_name = \app\Env::key('modules.path').$name;
		if (\file_exists($module_name) && ! $forced)
		{
			$this->writer
				->printf('error', "The module [$module_name] already exists.")->eol()
				->printf('status', 'Help', 'Use --forced to overwrite existsing files.')->eol();
			return;
		}

		$ds = DIRECTORY_SEPARATOR;

		// create the directory structure
		$dir = $module_name;
		\file_exists($dir) or \mkdir($dir, 0777, true); # base dir
		$app_dir = $dir.$ds.\mjolnir\cfs\CFSInterface::APPDIR;
		\file_exists($app_dir) or \mkdir($app_dir, 0777, true); # App dir
		$config_dir = $app_dir.$ds.\mjolnir\cfs\CFSInterface::CNFDIR;
		\file_exists($config_dir) or \mkdir($config_dir, 0777, true); # +App/config

//		if ( ! $mockup_template && ! $sandbox_template)
//		{
//			$lang_dir = $config_dir.$ds.'lang';
//			\file_exists($lang_dir) or \mkdir($lang_dir, 0777, true); # +App/config/lang
//			$test_dir = $app_dir.$ds.'features';
//			\file_exists($test_dir) or \mkdir($test_dir, 0777, true); # +App/features
//		}

		// create App/config/version
		\file_put_contents
			(
				$config_dir.$ds.'version'.EXT,
				static::version_file(\ltrim($namespace, '\\'))
			);

		if ($mockup_template)
		{
			$mjolnir_config_dir = $config_dir.$ds.'mjolnir';
			\file_exists($mjolnir_config_dir) or \mkdir($mjolnir_config_dir, 0777, true); # +App/config

			// allow admin and mockup access to guest
			\file_put_contents
				(
					$mjolnir_config_dir.$ds.'access'.EXT,
					static::access_file()
				);

			// enable mockup route
			\file_put_contents
				(
					$mjolnir_config_dir.$ds.'relays'.EXT,
					static::mockup_relays_file()
				);
		}

		if ($sandbox_template)
		{
			// create sandbox relay
			\file_put_contents
				(
					$app_dir.$ds.'relays'.EXT,
					static::sandbox_file()
				);

			$mjolnir_config_dir = $config_dir.$ds.'mjolnir';
			\file_exists($mjolnir_config_dir) or \mkdir($mjolnir_config_dir, 0777, true); # +App/config

			// enable mockup route
			\file_put_contents
				(
					$mjolnir_config_dir.$ds.'relays'.EXT,
					static::sandbox_relays_file()
				);
		}

		// create .gitignore
		\file_put_contents
			(
				$dir.$ds.'.gitignore',
				\mjolnir\cfs\CFSInterface::APPDIR.'/honeypot*'
			);

		// print notice
		$this->writer
			->printf('status', 'Info', 'Module created!')->eol()
			->printf('status', 'Help', 'To enable it, in the modules section of etc/environment.php add:')->eol()
			->printf('status', null, '$modpath.\''.$name.'\' => \''.\ltrim($namespace, '\\').'\',')->eol();
	}

} # class
