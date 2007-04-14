<?php
set_include_path(get_include_path().PATH_SEPARATOR.realpath('../'));
require_once('SiTech.php');
SiTech::loadClass('Test_Abstract');

class Test_Session extends Test_Abstract
{
	public function __construct($type, $arg = null)
	{
		SiTech::loadClass($type);
		if (is_null($arg)) {
			$this->_obj = new $type();
		} else {
			$this->_obj = new $type($arg);
		}
	}
}
?>
