<?php
/**
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_ConfigParser
 */

/**
 * @see SiTech_ConfigParser
 */
require_once('SiTech/ConfigParser.php');
/**
 * @see SiTech_ConfigParser_Interface
 */
require_once('SiTech/ConfigParser/Interface.php');

/**
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_ConfigParser_Base
 * @package SiTech_ConfigParser
 */
abstract class SiTech_ConfigParser_Base implements SiTech_ConfigParser_Interface
{
	protected $_attributes = array(
		0	=> false,
		1	=> 0
	);
	
	protected $_config = array();
	
	protected $_error;
	
	/**
	 * __construct()
	 *
	 * @param array $options Array of SiTech_ConfigParser::ATTR_* options to set.
	 */
	public function __construct(array $options)
	{
		foreach ($options as $attr => $value) {
			$this->setAttribute($attr, $value);
		}
	}
	
	/**
	 * __get()
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function __get($var)
	{
		switch ($var) {
			case 'error':
				$val = $this->_error;
				break;
				
			default:
				$val = null;
				break;
		}
		
		return($val);
	}
	
	/**
	 * Add a section to the current configuration.
	 *
	 * @param string $section Section name to remove.
	 * @return bool
	 */
	public function addSection($section)
	{
		if (!$this->hasSection($section)) {
			$this->_config[$section] = array();
			return(true);
		} else {
			$this->_handleError('Cannot add section "%s" because it already exists', array($section));
			return(false);
		}
	}
	
	/**
	 * Return an array containing instance wide defaults.
	 *
	 * @return array
	 */
	public function defaults()
	{
		return(array());
	}
	
	/**
	 * Get the specified option value for the named section. This default
	 * method returns all values as what they are in the config.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return mixed
	 */
	public function get($section, $option)
	{
		if ($this->hasSection($section)) {
			if ($this->hasOption($section, $option)) {
				return($this->_config[$section][$option]);
			} else {
				$this->_handleError('Cannot retreive value for option "%s" because it does not exist', array($option));
			}
		} else {
			$this->_handleError('Cannot retreive value for option "%s" because section "%s" does not exist', array($option, $section));
		}
		
		return(null);
	}
	
	/**
	 * Get the value for an attribute of the config parser.
	 *
	 * @param int $attr SiTech_ConfigParser::ATTR_* constant
	 * @return mixed
	 */
	public function getAttribute($attr)
	{
		return($this->_attributes[$attr]);
	}
	
	/**
	 * Get the specified option for the named section. Returns value as
	 * a boolean type.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return bool
	 */
	public function getBool($section, $option)
	{
		return((bool)$this->get($section, $option));
	}
	
	/**
	 * Get the specified option for the named section. Returns value as
	 * a float type.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return float
	 */
	public function getFloat($section, $option)
	{
		return((float)$this->get($section, $option));
	}
	
	/**
	 * Get the specified option for the named section. Returns value as
	 * an integer type.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return int
	 */
	public function getInt($section, $option)
	{
		return((int)$this->get($section, $option));
	}
	
	/**
	 * Check if the current configuration has the specified option within
	 * the specified section.
	 *
	 * @param string $section Section to find option in.
	 * @param string $option Option to see if exists.
	 * @return bool
	 */
	public function hasOption($section, $option)
	{
		if ($this->hasSection($section) && isset($this->_config[$section][$option])) {
			return(true);
		} else {
			return(false);
		}
	}
	
	/**
	 * Check if the current configuration has the specified section.
	 *
	 * @param string $section Section to check if exists.
	 * @return bool
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
	 * Return an array of values in a section with the option name as
	 * the key of the value. If section doesn't exist, returns false.
	 *
	 * @param string $section Section name to grab items from.
	 * @return array
	 */
	public function items($section)
	{
		if ($this->hasSection($section)) {
			return($this->_config[$section]);
		} else {
			$this->_handleError('Cannot retreive items because section "%s" does not exist', array($section));
			return(false);
		}
	}
	
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
	public function makeGlobal($section, $option = null, $reference = false)
	{
		$reference = (bool) $reference;
		
		if ($this->hasSection($section)) {
			if (!empty($option)) {
				if ($this->hasOption($section, $option)) {
					if ($reference) {
						$GLOBALS[$option] =& $this->_config[$section][$option];
					} else {
						$GLOBALS[$option] = $this->_config[$section][$option];
					}
				} else {
					$this->_handleError('Cannot make option "%s" of section "%s" global because the option does not exist', array($option, $section));
				}
			} else {
				if ($reference) {
					$GLOBALS[$section] =& $this->_config[$section];
				} else {
					$GLOBALS[$section] = $this->_config[$section];
				}
			}
		} else {
			if (empty($option)) {
				$this->_handleError('Cannot make section "%s" global because it does not exist', array($section));
			} else {
				$this->_handleError('Cannot make option "%s" of section "%s" global because the section does not exist', array($option, $section));
			}
		}
	}
	
	/**
	 * Return an array of all options in a section. If section does not
	 * exist, method returns false.
	 *
	 * @param string $section
	 * @return array
	 */
	public function options($section)
	{
		if ($this->hasSection($section)) {
			return(array_keys($this->_config[$section]));
		} else {
			$this->_handleError('Cannot retreive options because section "%s" does not exist', array($section));
			return(false);
		}
	}
	
	/**
	 * Remove the option from the specified section of the configuration.
	 *
	 * @param string $section
	 * @param string $option
	 * @return bool
	 */
	public function removeOption($section, $option)
	{
		if ($this->hasSection($section)) {
			if ($this->hasOption($option)) {
				unset($this->_config[$section][$option]);
				return(true);
			} else {
				$this->_handleError('Cannot remove the option "%s" from section "%s" because it does not exist', array($option, $section));
				return(false);
			}
		} else {
			$this->_handleError('Cannot remove option "%s" from section "%s" because the section does not exist', array($option, $section));
			return(false);
		}
	}
	
	/**
	 * Remove the specified section and all options within the section.
	 *
	 * @param string $section
	 * @return bool
	 */
	public function removeSection($section)
	{
		if ($this->hasSection($section)) {
			unset($this->_config[$section]);
			return(true);
		} else {
			$this->_handleError('Cannot remove section "%s" because it does not exist', array($section));
			return(false);
		}
	}
	
	/**
	 * Return an array of all sections in the configuration.
	 *
	 * @return array
	 */
	public function sections()
	{
		return(array_keys($this->_config));
	}
	
	/**
	 * Set a new option to the configuration. All values will be converted
	 * to strings.
	 *
	 * @param string $section
	 * @param string $option
	 * @param mixed $value
	 * @return bool
	 */
	public function set($section, $option, $value)
	{
		if ($this->hasSection($section)) {
			if ($this->hasOption($section, $option)) {
				$this->_handleError('Strict mode in effect - Cannot overwrite option "%s" in section "%s"', array($option, $section));
			}
			
			$this->_config[$section][$value] = (string)$value;
			return(true);
		} else {
			$this->_handleError('Cannot set option "%s". No section named "%s" exists in configuration!', array($section, $option));
			return(false);
		}
	}
	
	/**
	 * Set an attribute for the configuration parser.
	 *
	 * @param int $attr SiTech_ConfigParser::ATTR_* constant
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attr, $value)
	{
		$ret = true;
		
		switch ($attr)
		{
			case SiTech_ConfigParser::ATTR_STRICT:
				$this->_attributes[$attr] = (bool)$value;
				break;
				
			case SiTech_ConfigParser::ATTR_ERRMODE:
				switch ($value) {
					case SiTech_ConfigParser::ERRMODE_EXCEPTION:
					case SiTech_ConfigParser::ERRMODE_SILENT:
					case SiTech_ConfigParser::ERRMODE_WARNING:
						$this->_attributes[$attr] = $value;
						break;
						
					default:
						$this->_handleError('Invalid error mode setting for %s::ATTR_ERRMODE', array(__CLASS__));
						$ret = false;
						break;
				}
				break;
			
			default:
				$ret = false;
				break;
		}
		
		return($ret);
	}
	
	/**
	 * Handle an error message based on the current ATTR_ERRMODE level.
	 *
	 * @param string $string
	 * @param array $array
	 */
	protected function _handleError($string, array $array = array())
	{
		if ($this->getAttribute(SiTech_ConfigParser::ATTR_ERRMODE) === SiTech_ConfigParser::ERRMODE_EXCEPTION) {
			throw new SiTech_Exception($string, $array);
		} elseif ($this->getAttribute(SiTech_ConfigParser::ATTR_ERRMODE) === SiTech_ConfigParser::ERRMODE_WARNING) {
			trigger_error(vsprintf($string, $array), E_USER_WARNING);
		}
		
		$this->_error = vsprintf($string, $array);
	}
}
?>
