<?php
/**
 *
 */

/**
 * @see SiTech_Template
 */
require_once('SiTech/Template.php');

/**
 * @see SiTech_Template_Base
 */
require_once('SiTech/Template/Base.php');

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
     * Render the template file and return the contents.
     *
     * @return string
     * @see SiTech_Template_Interface::render()
     * @see SiTech_Template_Base::render()
     * @throws SiTech_Template_Exception
     */
    public function render ()
    {
    	$incPath = set_include_path($this->_path.PATH_SEPARATOR.get_include_path());
    	extract($this->_vars, EXTR_OVERWRITE);

    	if ($this->getAttribute(SiTech_Template::ATTR_STRICT)) {
    		$errReporting = error_reporting(E_ALL);
    	} else {
    		$errReporting = error_reporting(E_ALL & ~E_NOTICE);
    	}

    	if (!SiTech::isReadable($this->_file)) {
    		set_include_path($incPath);
    		error_reporting($errReporting);
    		SiTech::loadClass('SiTech_Template_Exception');
    		throw new SiTech_Template_Exception('Could not load template file "%s" on path "%s"', array($this->_file, $this->_path));
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
