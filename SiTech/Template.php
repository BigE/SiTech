<?php
require_once('SiTech.php');
SiTech::loadClass('SiTech_Factory');

class SiTech_Template extends SiTech_Factory
{
	const ATTR_STRICT = 0;
	
	const TYPE_PHP = 'SiTech_Template_PHP';
	
	/**
	 * __construct()
	 *
	 * @param int $type SiTech_Template::TYPE_* constant
	 */
	public function __construct($type = null)
	{
		if (empty($type)) {
			$type = self::TYPE_PHP;
		}
		
		$this->_objBackend = new $type();
	}
}
?>