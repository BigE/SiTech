<?php
require_once('SiTech.php');
require_once('SiTech/Factory.php');

/**
 *
 */
class SiTech_Session extends SiTech_Factory
{
	const ATTR_COOKIE_DOMAIN = 0;
	const ATTR_COOKIE_PATH = 1;
	const ATTR_COOKIE_TIME = 2;
	const ATTR_NAME = 3;
	const ATTR_REMEMBER = 4;
	const ATTR_STRICT = 5;
	const TYPE_FILE = 'SiTech_Session_File';
	
	function __construct ($type = null)
	{
		if (empty($type)) {
			$type = self::TYPE_FILE;
		}
		
		SiTech::loadClass($type);
		$this->_objBackend = new $type();
	}
}
?>