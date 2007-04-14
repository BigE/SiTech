<?php
/**
 * SiTech ConfigParser class
 * 
 * @package SiTech_ConfigParser
 */

/**
 * SiTech base functionality
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_Factory');

/**
 * Configuration parsing that supports multiple formats. Currently supported
 * is INI based configs and XML based configs.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_ConfigParser
 * @package SiTech_ConfigParser
 */
class SiTech_ConfigParser extends SiTech_Factory
{
	/**
	 * Class constant for INI style configuration.
	 */
	const TYPE_INI = 'SiTech_ConfigParser_INI';
	/**
	 * Class constant for XML style configuration.
	 */
	const TYPE_XML = 'SiTech_ConfigParser_XML';
	
	/**
	 * Class constructor. The argument we take here will setup our child class
	 * to the specified type. All methods within the child class will be available
	 * using __call() magic.
	 *
	 * @param   const   Constant that specifies backend type.
	 */
	public function __construct($type=null, $vars=array())
	{
		if (is_null($type)) {
			$type = self::TYPE_INI;
		}

		SiTech::loadClass($type);
		$this->_type = $type;
		$this->_backend = new $type($vars);
	}
}
?>
