<?php namespace app;

// This is an IDE honeypot. It tells IDEs the class hirarchy, but otherwise has
// no effect on your application. :)

// HowTo: order honeypot -n 'mjolnir\cfs'

class CFS extends \mjolnir\cfs\CFS {}
class CFSCompatible extends \mjolnir\cfs\CFSCompatible {}
class Instantiatable extends \mjolnir\cfs\Instantiatable { /** @return \mjolnir\cfs\Instantiatable */ static function instance() { return parent::instance(); } }
class Kohana3_Bridge extends \mjolnir\cfs\Kohana3_Bridge {}
class Mjolnir extends \mjolnir\cfs\Mjolnir {}
class Overlord extends \mjolnir\cfs\Overlord { /** @return \mjolnir\cfs\Overlord */ static function instance() { return parent::instance(); } }
class SilentWriter extends \mjolnir\cfs\SilentWriter { /** @return \mjolnir\cfs\SilentWriter */ static function instance() { return parent::instance(); } }
class Task_Behat extends \mjolnir\cfs\Task_Behat { /** @return \mjolnir\cfs\Task_Behat */ static function instance() { return parent::instance(); } }
class Task_Cleanup extends \mjolnir\cfs\Task_Cleanup { /** @return \mjolnir\cfs\Task_Cleanup */ static function instance() { return parent::instance(); } }
class Task_Compile extends \mjolnir\cfs\Task_Compile { /** @return \mjolnir\cfs\Task_Compile */ static function instance() { return parent::instance(); } }
class Task_Find_Class extends \mjolnir\cfs\Task_Find_Class { /** @return \mjolnir\cfs\Task_Find_Class */ static function instance() { return parent::instance(); } }
class Task_Find_File extends \mjolnir\cfs\Task_Find_File { /** @return \mjolnir\cfs\Task_Find_File */ static function instance() { return parent::instance(); } }
class Task_Honeypot extends \mjolnir\cfs\Task_Honeypot { /** @return \mjolnir\cfs\Task_Honeypot */ static function instance() { return parent::instance(); } }
class Task_Make_Class extends \mjolnir\cfs\Task_Make_Class { /** @return \mjolnir\cfs\Task_Make_Class */ static function instance() { return parent::instance(); } }
class Task_Make_Config extends \mjolnir\cfs\Task_Make_Config { /** @return \mjolnir\cfs\Task_Make_Config */ static function instance() { return parent::instance(); } }
class Task_Make_Module extends \mjolnir\cfs\Task_Make_Module { /** @return \mjolnir\cfs\Task_Make_Module */ static function instance() { return parent::instance(); } }
class Task_Make_Trait extends \mjolnir\cfs\Task_Make_Trait { /** @return \mjolnir\cfs\Task_Make_Trait */ static function instance() { return parent::instance(); } }
class Task_Status extends \mjolnir\cfs\Task_Status { /** @return \mjolnir\cfs\Task_Status */ static function instance() { return parent::instance(); } }
class Task_Versions extends \mjolnir\cfs\Task_Versions { /** @return \mjolnir\cfs\Task_Versions */ static function instance() { return parent::instance(); } }
class Task extends \mjolnir\cfs\Task {}
class Writer extends \mjolnir\cfs\Writer { /** @return \mjolnir\cfs\Writer */ static function instance() { return parent::instance(); } }
