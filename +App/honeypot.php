<?php namespace app;

// This is an IDE honeypot. It tells IDEs the class hirarchy, but otherwise has
// no effect on your application. :)

// HowTo: order honeypot -n 'mjolnir\cfs'


class Backend_FilePermissions extends \mjolnir\cfs\Backend_FilePermissions
{
	/** @return \app\Backend_FilePermissions */
	static function instance() { return parent::instance(); }
}

class Benchmark extends \mjolnir\cfs\Benchmark
{
}

class BenchmarkInterface extends \mjolnir\cfs\BenchmarkInterface
{
}

class CFS extends \mjolnir\cfs\CFS
{
}

class CFSInterface extends \mjolnir\cfs\CFSInterface
{
}

class Env extends \mjolnir\cfs\Env
{
	/** @return \app\Env */
	static function set($key, $value) { return parent::set($key, $value); }
}

/**
 * @method \app\Environment set($key, $value)
 */
class Environment extends \mjolnir\cfs\Environment
{
}

class Instantiatable extends \mjolnir\cfs\Instantiatable
{
	/** @return \app\Instantiatable */
	static function instance() { return parent::instance(); }
}

class Kohana3_Bridge extends \mjolnir\cfs\Kohana3_Bridge
{
}

class Mjolnir extends \mjolnir\cfs\Mjolnir
{
}

/**
 * @method \app\Overlord writer_is($writer)
 * @method \app\Writer writer()
 */
class Overlord extends \mjolnir\cfs\Overlord
{
	/** @return \app\Overlord */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\SilentWriter eol()
 * @method \app\SilentWriter writef($format)
 * @method \app\SilentWriter stderr_writef($format)
 * @method \app\SilentWriter printf($format)
 * @method \app\SilentWriter addformat($format, $formatter)
 * @method \app\SilentWriter stdout_is($resource)
 * @method \app\SilentWriter stderr_is($resource)
 * @method \app\SilentWriter set($name, $value)
 * @method \app\SilentWriter add($name, $value)
 * @method \app\SilentWriter metadata_is(array $metadata = null)
 */
class SilentWriter extends \mjolnir\cfs\SilentWriter
{
	/** @return \app\SilentWriter */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Base set($name, $value)
 * @method \app\Task_Base add($name, $value)
 * @method \app\Task_Base metadata_is(array $metadata = null)
 * @method \app\Task_Base writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Base extends \mjolnir\cfs\Task_Base
{
	/** @return \app\Task_Base */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Behat set($name, $value)
 * @method \app\Task_Behat add($name, $value)
 * @method \app\Task_Behat metadata_is(array $metadata = null)
 * @method \app\Task_Behat writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Behat extends \mjolnir\cfs\Task_Behat
{
	/** @return \app\Task_Behat */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Bower set($name, $value)
 * @method \app\Task_Bower add($name, $value)
 * @method \app\Task_Bower metadata_is(array $metadata = null)
 * @method \app\Task_Bower writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Bower extends \mjolnir\cfs\Task_Bower
{
	/** @return \app\Task_Bower */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Cleanup set($name, $value)
 * @method \app\Task_Cleanup add($name, $value)
 * @method \app\Task_Cleanup metadata_is(array $metadata = null)
 * @method \app\Task_Cleanup writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Cleanup extends \mjolnir\cfs\Task_Cleanup
{
	/** @return \app\Task_Cleanup */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Compile set($name, $value)
 * @method \app\Task_Compile add($name, $value)
 * @method \app\Task_Compile metadata_is(array $metadata = null)
 * @method \app\Task_Compile writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Compile extends \mjolnir\cfs\Task_Compile
{
	/** @return \app\Task_Compile */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Config set($name, $value)
 * @method \app\Task_Config add($name, $value)
 * @method \app\Task_Config metadata_is(array $metadata = null)
 * @method \app\Task_Config writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Config extends \mjolnir\cfs\Task_Config
{
	/** @return \app\Task_Config */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Find_Class set($name, $value)
 * @method \app\Task_Find_Class add($name, $value)
 * @method \app\Task_Find_Class metadata_is(array $metadata = null)
 * @method \app\Task_Find_Class writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Find_Class extends \mjolnir\cfs\Task_Find_Class
{
	/** @return \app\Task_Find_Class */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Find_Config set($name, $value)
 * @method \app\Task_Find_Config add($name, $value)
 * @method \app\Task_Find_Config metadata_is(array $metadata = null)
 * @method \app\Task_Find_Config writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Find_Config extends \mjolnir\cfs\Task_Find_Config
{
	/** @return \app\Task_Find_Config */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Find_File set($name, $value)
 * @method \app\Task_Find_File add($name, $value)
 * @method \app\Task_Find_File metadata_is(array $metadata = null)
 * @method \app\Task_Find_File writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Find_File extends \mjolnir\cfs\Task_Find_File
{
	/** @return \app\Task_Find_File */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Honeypot set($name, $value)
 * @method \app\Task_Honeypot add($name, $value)
 * @method \app\Task_Honeypot metadata_is(array $metadata = null)
 * @method \app\Task_Honeypot writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Honeypot extends \mjolnir\cfs\Task_Honeypot
{
	/** @return \app\Task_Honeypot */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Log_Short set($name, $value)
 * @method \app\Task_Log_Short add($name, $value)
 * @method \app\Task_Log_Short metadata_is(array $metadata = null)
 * @method \app\Task_Log_Short writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Log_Short extends \mjolnir\cfs\Task_Log_Short
{
	/** @return \app\Task_Log_Short */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Make_Class set($name, $value)
 * @method \app\Task_Make_Class add($name, $value)
 * @method \app\Task_Make_Class metadata_is(array $metadata = null)
 * @method \app\Task_Make_Class writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Make_Class extends \mjolnir\cfs\Task_Make_Class
{
	/** @return \app\Task_Make_Class */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Make_Module set($name, $value)
 * @method \app\Task_Make_Module add($name, $value)
 * @method \app\Task_Make_Module metadata_is(array $metadata = null)
 * @method \app\Task_Make_Module writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Make_Module extends \mjolnir\cfs\Task_Make_Module
{
	/** @return \app\Task_Make_Module */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Make_Trait set($name, $value)
 * @method \app\Task_Make_Trait add($name, $value)
 * @method \app\Task_Make_Trait metadata_is(array $metadata = null)
 * @method \app\Task_Make_Trait writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Make_Trait extends \mjolnir\cfs\Task_Make_Trait
{
	/** @return \app\Task_Make_Trait */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Status set($name, $value)
 * @method \app\Task_Status add($name, $value)
 * @method \app\Task_Status metadata_is(array $metadata = null)
 * @method \app\Task_Status writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Status extends \mjolnir\cfs\Task_Status
{
	/** @return \app\Task_Status */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_Versions set($name, $value)
 * @method \app\Task_Versions add($name, $value)
 * @method \app\Task_Versions metadata_is(array $metadata = null)
 * @method \app\Task_Versions writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Versions extends \mjolnir\cfs\Task_Versions
{
	/** @return \app\Task_Versions */
	static function instance() { return parent::instance(); }
}

class Task extends \mjolnir\cfs\Task
{
	/** @return \app\Task */
	static function invoke($encoded_task) { return parent::invoke($encoded_task); }
}

/**
 * @method \app\Writer eol()
 * @method \app\Writer writef($format)
 * @method \app\Writer stderr_writef($format)
 * @method \app\Writer printf($format)
 * @method \app\Writer addformat($format, $formatter)
 * @method \app\Writer stdout_is($resource)
 * @method \app\Writer stderr_is($resource)
 * @method \app\Writer set($name, $value)
 * @method \app\Writer add($name, $value)
 * @method \app\Writer metadata_is(array $metadata = null)
 */
class Writer extends \mjolnir\cfs\Writer
{
	/** @return \app\Writer */
	static function instance() { return parent::instance(); }
}
