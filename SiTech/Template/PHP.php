<?php
/**
 * PHP template class.
 *
 * @package SiTech_Template
 */

/**
 * Grab the base SiTech class
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_Template_Base');

/**
 * PHP type template class.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Template_PHP
 * @package SiTech_Template
 */
class SiTech_Template_PHP extends SiTech_Template_Base
{
	/**
	 * Display the template to the current output.
	 *
	 * @throws SiTech_Template_Exception
	 */
	public function render()
	{
		$incPath = set_include_path($this->_path);
		extract($this->_vars, EXTR_OVERWRITE);
		$errReporting = error_reporting(E_ALL);
		
		if (!SiTech::isReadable($this->_file)) {
			set_include_path($incPath);
			error_reporting($errReporting);
			SiTech::loadClass('SiTech_Template_Exception');
			throw new SiTech_Template_Exception('Could not load template file "%s" on path "%s".', array($this->_file, $this->_path));
		}

		ob_start();
		include($this->_file);
		$output = ob_get_clean();

		set_include_path($incPath);
		error_reporting($errReporting);

		return($output);
	}
}
?>
