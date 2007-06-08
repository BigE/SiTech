<?php
/**
 * SiTech Template class.
 *
 * @package SiTech_Template
 * @version $Id$
 */

/**
 * SiTech base functionality
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_Factory');

/**
 * Template class for SiTech library. Many types of templates will be
 * made available through here. For starts, we just have a very basic but
 * functional PHP style template available.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Template
 * @package SiTech_Template
 */
class SiTech_Template extends SiTech_Factory
{
	/**
	 * Class constant for PHP style templates.
	 */
	const TYPE_PHP = 'SiTech_Template_PHP';

	/**
	 * Basic constructor. This passes the template file name into the backend class
	 * specified by $type
	 *
	 * @param string $templateFile Template file name to load.
	 * @param string $type Class name for the backend type. Can be one of this class constants.
	 */
	public function __construct($templateFile, $type=null, $templatePath=null)
	{
		if (is_null($type)) {
			$type = self::TYPE_PHP;
		}

		SiTech::loadClass($type);
		$this->_backend = new $type($templateFile, $templatePath);
	}
}
?>
