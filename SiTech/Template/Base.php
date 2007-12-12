<?php
require_once('SiTech.php');
SiTech::loadInterface('SiTech_Template_Interface');

/**
 * 
 */
abstract class SiTech_Template_Base implements SiTech_Template_Interface
{
	protected $_attributes = array();

	/**
	 * Template file to load.
	 *
	 * @var string
	 */
	protected $_file;
	
	/**
	 * Path to where to find the template.
	 *
	 * @var string
	 */
	protected $_path;
	
	/**
	 * Varables for the template.
	 *
	 * @var array
	 */
	protected $_vars = array();
	
	/**
	 * __construct()
	 * 
	 * @param string $file File to load as template 
	 * @param string $path Path to load file from 
	 * @see SiTech_Template_Interface::__construct()
	 */
	public function __construct ( $file , $path = null )
	{
		$this->_path = $path;
		$this->_file = $file;
	}
	
	/**
	 * 
	 * @param string $variable Variable name. 
	 * @param mixed $value Value to set to variable. 
	 * @see SiTech_Template_Interface::assign()
	 */
	public function assign ($variable , $value)
	{
		if (isset($this->_vars[$variable]) && $this->_getAttribute(SiTech_Template::ATTR_STRICT)) {
			SiTech::loadClass('SiTech_Template_Exception');
			throw new SiTech_Template_Exception('Strict mode in effect - variable %s is already set', array($variable));
		}
		
		$this->_vars[$variable] = $value;
	}
	
	/**
	 * Output the template file completely rendered.
	 * 
	 * @see SiTech_Template_Interface::display()
	 */
	public function display ()
	{
		echo $this->render();
	}

	public function getAttribute($attribute)
	{
		if (isset($this->_attributes[$attribute])) {
			return($this->_attributes[$attribute]);
		} else {
			return(null);
		}
	}

	public function setAttribute($attribute, $value)
	{
		$this->_attributes[$attribute] = $value;
	}
	
	/**
	 * Set the template path to find the template at.
	 * 
	 * @param string $path Path to find template at. 
	 * @see SiTech_Template_Interface::setPath()
	 */
	public function setPath ( $path )
	{
		$this->_path = $path;
	}
	
	/**
	 * Unset the specified variable.
	 * 
	 * @param string $variable Variable to remove. 
	 * @see SiTech_Template_Interface::unassign()
	 */
	public function unassign ( $variable )
	{
		if (isset($this->_vars[$variable])) {
			unset($this->_vars[$variable]);
		}
	}
}

?>
