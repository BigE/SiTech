<?php
/**
 * Base support for SiTech_ConfigParser
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_ConfigParser
 * @version $Id$
 */

/**
 * SiTech include.
 */
require_once('SiTech.php');
SiTech::loadInterface('SiTech_ConfigParser_Interface');

/**
 * Base class for all config parsing types. Here base functionality is
 * created for all classes to use.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_ConfigParser_Base
 * @package SiTech_ConfigParser
 */
abstract class SiTech_ConfigParser_Base implements SiTech_ConfigParser_Interface
{
	protected $_config = array();

	private $_vars = array();

	/**
	 * Class constructor. The variables passed are used when parsing the template. For
	 * a complete description of how variables work, please see the README.
	 *
	 * @access  public
	 * @param   array   Array of default variables.
	 */
	public function __construct($vars=array())
	{
		/* typecast to save problems later */
		$this->_vars = array_merge((array)$vars, $_ENV);
	}

	/**
	 * Add a section to the configuration. If the section already exists, an exception
	 * is thrown.
	 *
	 * @access  public
	 * @param   string  Section name
	 */
	public function addSection($section)
	{
		if ($this->hasSection($section)) {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('The configuration does not have the section "%s".', $section);
		} else {
			$this->_config[$section] = array();
		}
	}

	/**
	 * Return an array of default variables set within the class.
	 *
	 * @access  public
	 * @return  array
	 */
	public function defaults()
	{
		return($this->_vars);
	}

	/**
	 * Get a specific option from the configuration. If the section specified does not
	 * exist, throw an exception.
	 *
	 * @access  public
	 * @param   string  Section name
	 * @param   string  Option name
	 * @return  mixed
	 */
	public function get($section, $option, $raw=false, $vars=array())
	{
		if ($this->hasOption($section, $option)) {
			if ($raw === true) {
				return($this->_config[$section][$option]);
			} else {
				$vars = array_merge($vars, $this->_vars);
				$keys = array_keys($vars);
				$vars = array_values($vars);
				return(str_replace($keys, $vars, $this->_config[$section][$option]));
			}
		} else {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('The option "%s" was not available in the configuration.', $option);
		}
	}

	/**
	 * Return the value as an int value
	 *
	 * @access  public
	 * @param   string  Section name to fetch option from
	 * @param   string  Option name to grab
	 * @return  int
	 */
	public function getInt($section, $option, $raw=false, $vars=array())
	{
		return((int)$this->get($section, $option));
	}

	/**
	 * Return the value as a float value.
	 *
	 * @access  public
	 * @param   string  Section name to fetch option from
	 * @param   string  Option name to grab
	 * @return  float
	 */
	public function getFloat($section, $option, $raw=false, $vars=array())
	{
		return((float)$this->get($section, $option));
	}

	/**
	 * Return the value as a boolean value.
	 *
	 * @access  public
	 * @param   string  Section name to fetch option from
	 * @param   string  Option name to grab
	 * @return  bool
	 */
	public function getBool($section, $option, $raw=false, $vars=array())
	{
		return((bool)$this->get($section, $option));
	}

	/**
	 * Test to see if the specified option exists in a section. If the section or option
	 * does not exist, return false, otherwise return true.
	 *
	 * @access  public
	 * @param   string  Section name where option exists
	 * @param   string  Option name to check
	 * @return  bool
	 */
	public function hasOption($section, $option)
	{
		if ($this->hasSection($section)) {
			if (isset($this->_config[$section][$option])) {
				return(true);
			} else {
				return(false);
			}
		} else {
			return(false);
		}
	}

	/**
	 * Test if the configuration has the specified section. True if it does and
	 * False if it doesn't.
	 *
	 * @access  public
	 * @param   string  Section name to check
	 * @return  bool
	 */
	public function hasSection($section)
	{
		if (isset($this->_config[$section])) {
			return(true);
		} else {
			return(false);
		}
	}

	/**
	 * Return an array of array(options, values) of all configuration options in the
	 * specified section. If the section does not exist, an exception is thrown.
	 *
	 * @access  public
	 * @param   string  Section name to get values from
	 * @return  array
	 */
	public function items($section)
	{
		if ($this->hasSection($section)) {
			$options = array_keys($this->_config[$section]);
			$values = array_values($this->_config[$section]);
			return(array($options, $values));
		} else {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('Could not find the section "%s" in the configuration.', $section);
		}
	}

	/**
	 * Return an array of options from the specified section. If the section does not
	 * exist, then an exception is thrown.
	 *
	 * @access  public
	 * @param   string  Section name to grab options from
	 * @return  mixed
	 */
	public function options($section)
	{
		if ($this->hasSection($section)) {
			return($this->_config[$section]);
		} else {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('The section "%s" does not exist in the configuration.', $section);
		}
	}

	/**
	 * Read in the configuration. This will do all the checks to see if the file exists and
	 * is readable. If it isn't, an exception will be thrown.
	 *
	 * @access  public
	 * @param   string  Filename of configuration.
	 */
	public function read($file)
	{
		if (file_exists($file)) {
			if (is_readable($file)) {
				$this->_read($file);
			} else {
				SiTech::loadClass('SiTech_ConfigParser_Exception');
				throw new SiTech_ConfigParser_Exception('The configuration file "%s" is not readable.', $file);
			}
		} else {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('The configuration file "%s" could not be found.', $file);
		}
	}

	/**
	 * Remove an option from the configuration. If the specified section is not found
	 * then an exception is thrown.
	 *
	 * @access  public
	 * @param   string  Section name that option is in
	 * @param   string  Option name to remove
	 */
	public function removeOption($section, $option)
	{
		if ($this->hasSection($section)) {
			if ($this->hasOption($option)) {
				unset($this->_config[$section][$option]);
			} else {
				SiTech::loadClass('SiTech_ConfigParser_Exception');
				throw new SiTech_ConfigParser_Exception('The configuration does not contain the option "%s" in the section "%s".', $option, $section);
			}
		} else {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('The configuration does not contain the section "%s".', $section);
		}
	}

	/**
	 * Remove a section from the configuration. If the section is not found an
	 * exception is thrown.
	 *
	 * @access  public
	 * @param   string  Section name to remove from configuration
	 */
	public function removeSection($section)
	{
		if ($this->hasSection($section)) {
			unset($this->_config[$section]);
		} else {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('The configuration does not containt the section "%s".', $section);
		}
	}

	/**
	 * Returns an array of all the sections in the configuration.
	 *
	 * @access  public
	 * @return  array
	 */
	public function sections()
	{
		return(array_keys($this->_config));
	}

	/**
	 * Set an option in the configuration into the specified section. If the section does
	 * not exist, an exception is thrown.
	 *
	 * @access  public
	 * @param   string  Section name to add option to
	 * @param   string  Option name to add
	 * @param   mixed   Value to assign to new option
	 */
	public function set($section, $option, $value)
	{
		if ($this->hasSection($section)) {
			$this->_config[$section][$option] = $value;
		} else {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('The configuration does not contain the section "%s".', $section);
		}
	}

	/**
	 */
	public function write($file)
	{

		/*if (!file_exists($file) || !is_writeable($file)) {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('Unable to write to configuration file "%s".', $file);
		}*/

		$this->_write($file);
	}

	abstract protected function _read($file);
	abstract protected function _write($file);
}
?>
