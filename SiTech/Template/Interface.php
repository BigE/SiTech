<?php

/**
 * Interface for SiTech_Template package.
 */
interface SiTech_Template_Interface
{
	/**
	 * __construct()
	 * 
	 * @param string $file File to load as template 
	 * @param string $path Path to load file from
	 */
	public function __construct ($file , $path = null);
	
	/**
	 * Assign a value to a variable in the template.
	 *
	 * @param string $variable Variable name.
	 * @param mixed $value Value to set to variable.
	 */
	public function assign($variable, $value);
	
	/**
	 * Output the template file completely rendered.
	 */
	public function display();
	
	/**
	 * Get an attribute setting for the template class.
	 *
	 * @param int $attribute SiTech_Template::ATTR_* constat
	 */
	public function getAttribute($attribute);
	
	/**
	 * Render the template file and return the contents.
	 *
	 * @return string
	 */
	public function render();
	
	/**
	 * Set attribute of the template class.
	 *
	 * @param int $attribute SiTech_Template::ATTR_* constant.
	 * @param mixed $value Value to set attribute.
	 */
	public function setAttribute($attribute, $value);
	
	/**
	 * Set the template path to find the template at.
	 *
	 * @param string $path Path to find template at.
	 */
	public function setPath($path);
	
	/**
	 * Unset the specified variable.
	 *
	 * @param string $variable Variable to remove.
	 */
	public function unassign($variable);
}

?>
