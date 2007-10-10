<?php
/**
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_ConfigParser
 */

/**
 * @see SiTech
 */
require_once('SiTech.php');
/**
 * @see SiTech_Factory
 */
SiTech::loadClass('SiTech_Factory');

/**
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_ConfigParser
 * @package SiTech_ConfigParser
 */
class SiTech_ConfigParser extends SiTech_Factory
{
	const ATTR_STRICT = 0;
	const ATTR_ERRMODE = 1;
	const ERRMODE_SILENT = 0;
	const ERRMODE_WARNING = 1;
	const ERRMODE_EXCEPTION = 2;
	const TYPE_INI = 'SiTech_ConfigParser_INI';
	const TYPE_XML = 'SiTech_ConfigParser_XML';
	
	public function __construct($type = null, $options = array())
	{
		if (empty($type)) {
			$type = self::TYPE_INI;
		}
		
		SiTech::loadClass($type);
		$this->_objBackend = new $type($options);
	}
}
?>