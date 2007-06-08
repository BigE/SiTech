<?php
/**
 * Base template class.
 *
 * @package SiTech_Template
 * @version $Id$
 */

/**
 * Grab the base SiTech class
 */
require_once('SiTech.php');
SiTech::loadInterface('SiTech_Template_Interface');

/**
 * Base template class to provide functionality to all template backends.
 *
 * @abstract
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Template_Base
 * @package SiTech_Template
 */
abstract class SiTech_Template_Base implements SiTech_Template_Interface
{
	/**
	 * Template file name to load when displaying.
	 *
	 * @var string
	 */
	protected $_file;

	/**
	 * Template file path to load files from. If SITECH_TEMPLATE_PATH is
	 * defined and this is null, its value will be taken as the base path
	 * for template files.
	 *
	 * @var string
	 */
	protected $_path;

	/**
	 * Set strict rules for templates. This will turn on notices (disabled
	 * by default) and disallow re-assigning of template variables.
	 *
	 * @var bool
	 */
	protected $_strict = false;

	/**
	 * Array of variables set for the template.
	 *
	 * @var array
	 */
	protected $_vars = array();

	/**
	 * Constructor.
	 *
	 * @param string $file Template filename to load.
	 * @param string $path Template path to load files from.
	 */
	public function __construct($file, $path=null)
	{
		$this->_file = $file;
		$this->_path = $path;
	}

	/**
	 * Assign a variable to the template.
	 *
	 * @param unknown_type $variable
	 * @param unknown_type $value
	 * @throws SiTech_Template_Exception
	 */
	public function assign($variable, $value)
	{
		if ($this->_strict && isset($this->_vars[$variable])) {
			SiTech::loadClass('SiTech_Template_Exception');
			throw new SiTech_Template_Exception('Strict standards are in effect, cannot re-assign "%s" until it is unassigned.', array($variable));
		}

		$this->_vars[$variable] = $value;
	}

	/**
	 * Output the rendered template to the current display.
	 */
	public function display()
	{
		echo $this->render();
	}

	/**
	 * Set strict rules for templates. This will turn on notices (disabled
	 * by default) and disallow re-assigning of template variables without
	 * using unassign.
	 *
	 * @param bool $bool True to set strict rules, false to disable.
	 */
	public function setStrict($bool)
	{
		$this->_strict = (bool)$bool;
	}

	/**
	 * Set the template path to load template files from.
	 *
	 * @param string $path Template path to load files from.
	 */
	public function setTemplatePath($path)
	{
		$this->_path = $path;
	}

	/**
	 * Remove a variable from the template.
	 *
	 * @param string $variable Variable name to remove.
	 */
	public function unassign($variable)
	{
		if (isset($this->_vars[$variable])) {
			unset($this->_vars[$variable]);
		}
	}
}
?>
