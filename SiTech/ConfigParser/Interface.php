<?php
interface SiTech_ConfigParser_Interface
{
	/**
	 * Add a section to the current configuration.
	 *
	 * @param string $section Section name to remove.
	 * @return bool
	 */
	public function addSection($section);
	
	/**
	 * Return an array containing instance wide defaults.
	 *
	 * @return array
	 */
	public function defaults();
	
	/**
	 * Get the specified option value for the named section. This default
	 * method returns all values as strings.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return string
	 */
	public function get($section, $option);
	
	/**
	 * Get the value for an attribute of the config parser.
	 *
	 * @param int $attr SiTech_ConfigParser::ATTR_* constant
	 * @return mixed
	 */
	public function getAttribute($attr);
	
	/**
	 * Get the specified option for the named section. Returns value as
	 * a boolean type.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return bool
	 */
	public function getBool($section, $option);
	
	/**
	 * Get the specified option for the named section. Returns value as
	 * a float type.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return float
	 */
	public function getFloat($section, $option);
	
	/**
	 * Get the specified option for the named section. Returns value as
	 * an integer type.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return int
	 */
	public function getInt($section, $option);
	
	/**
	 * Check if the current configuration has the specified option within
	 * the specified section.
	 *
	 * @param string $section Section to find option in.
	 * @param string $option Option to see if exists.
	 * @return bool
	 */
	public function hasOption($section, $option);
	
	/**
	 * Check if the current configuration has the specified section.
	 *
	 * @param string $section Section to check if exists.
	 * @return bool
	 */
	public function hasSection($section);
	
	/**
	 * Return an array of values in a section with the option name as
	 * the key of the value.
	 *
	 * @param string $section Section name to grab items from.
	 * @return array
	 */
	public function items($section);
	
	/**
	 * Make a whole section or just an option in a section global. This allows
	 * access to the specified section or option in the global scope. If the
	 * $reference argument is true, a direct reference will be created, meaning
	 * changes will be reflected.
	 *
	 * @param string $section
	 * @param string $option
	 * @param bool $reference
	 */
	public function makeGlobal($section, $option = null);
	
	/**
	 * Return an array of all options in a section.
	 *
	 * @param string $section
	 * @return array
	 */
	public function options($section);
	
	/**
	 * Read the specified file(s) into the configuration. Return value
	 * will be an array in filename => bool format.
	 *
	 * @param array $files Files to read into configuration.
	 * @return array
	 */
	public function read(array $files);
	
	/**
	 * Remove the option from the specified section of the configuration.
	 *
	 * @param string $section
	 * @param string $option
	 * @return bool
	 */
	public function removeOption($section, $option);
	
	/**
	 * Remove the specified section and all options within the section.
	 *
	 * @param string $section
	 * @return bool
	 */
	public function removeSection($section);
	
	/**
	 * Return an array of all sections in the configuration.
	 *
	 * @return array
	 */
	public function sections();
	
	/**
	 * Set a new option to the configuration. All values will be converted
	 * to strings.
	 *
	 * @param string $section
	 * @param string $option
	 * @param mixed $value
	 * @return bool
	 */
	public function set($section, $option, $value);
	
	/**
	 * Set an attribute for the configuration parser.
	 *
	 * @param int $attr SiTech_ConfigParser::ATTR_* constant
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attr, $value);
	
	/**
	 * Write the current configuration to a single specified file.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function write($file);
	
	protected function _handleError($string, $array = array());
}
?>