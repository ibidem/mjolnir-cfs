<?php namespace mjolnir\cfs;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2013 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Licenses extends \app\Task_Base
{
	/**
	 * ...
	 */
	function run()
	{
		$namespace = $this->get('namespace', false);

		if ($namespace === false)
		{
			$namespaces = \app\CFS::namespaces();
			foreach ($namespaces as $namespace => $path)
			{
				$this->print_licenseinfo($namespace, $path);
				$this->writer->eol()->eol()->eol();
			}
		}
		else # use given namespace
		{
			$this->print_licenseinfo($namespace, \app\CFS::modulepath($namespace));
		}
	}

	/**
	 * ...
	 */
	protected function print_licenseinfo($namespace, $path)
	{
		$this->writer->writef("\n $namespace \n%s\n\n", \str_repeat('-', \strlen($namespace) + 2));

		$filenames = ['license.md', 'license.txt', 'LICENSE.txt', 'LICENSE', 'license', 'LICENSE.md'];

		$found = false;
		foreach ($filenames as $file)
		{
			if (\file_exists($path.'/'.$file))
			{
				$this->writer
					->writef(\file_get_contents($path.'/'.$file))
					->eol();

				$found = true;
				break;
			}
		}

		if ( ! $found)
		{
			$this->writer->writef(" License information could not be detected.\n See: %s", \str_replace('\\', '/', $path))->eol();
		}
	}

} # class