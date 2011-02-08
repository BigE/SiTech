<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace SiTech;

/**
 * SiTech/ConfigParser - Configuration management class.
 *
 * This configuration class was closely modeled after the ConfigParser module
 * that is found in Python. I found it to be a very useful class and decided to
 * port the functionality over to this library.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group © 2008-2011
 * @filesource
 * @package SiTech_ConfigParser
 * @version $Id$
 */
class ConfigParser
{
	const ATTR_STRICT = 0;

	const ATTR_ERRMODE = 1;

	const ATTR_HANDLER = 2;

	const ERRMODE_SILENT = 0;

	const ERRMODE_WARNING = 1;

	const ERRMODE_EXCEPTION = 2;

	protected $_attributes = array();

	protected $_config = array();

	/**
	 * Constructor for config parser.
	 *
	 * @param array $options Array of self::ATTR_* options to set.
	 */
	protected function __construct(array $options = array())
	{
		foreach ($options as $attr => $value) {
			$this->setAttribute($attr, $value);
		}

		if (empty($this->_attributes[self::ATTR_HANDLER])) {
			/* default to INI files */
			$handler = self::HANDLER_INI;
			require_once(str_replace('_', DIRECTORY_SEPARATOR, $handler).'.php');
			$this->setAttribute(self::ATTR_HANDLER, new $handler);
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
	 * @param int $attr self::ATTR_* constant
	 * @return mixed
	 */
	public function getAttribute($attr)
	{
		return((isset($this->_attributes[$attr]))? $this->_attributes[$attr] : null);
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
	 * Load a new instance of the config parser. All options should be passed
	 * in through the array.
	 *
	 * @param array $options Array of self::ATTR_* options to set.
	 * @return SiTech_ConfigParser A new config parser instance.
	 */
	static public function load(array $options = array())
	{
		return(new self($options));
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

	public function read(array $items)
	{
		$ret = array();

		foreach ($items as $item) {
			list($bool, $config) = $this->_attributes[self::ATTR_HANDLER]->read($item);
			$ret[$item] = $bool;
			if (!$bool) {
				$this->_handleError('Unable to parse "%s" into the config', array($item));
			} else {
				$this->_config = array_merge($config, $this->_config);
			}
		}

		return($ret);
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

			$this->_config[$section][$option] = $value;
			return(true);
		} else {
			$this->_handleError('Cannot set option "%s". No section named "%s" exists in configuration!', array($section, $option));
			return(false);
		}
	}

	/**
	 * Set an attribute for the configuration parser.
	 *
	 * @param int $attr self::ATTR_* constant
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attr, $value)
	{
		$ret = true;

		switch ($attr)
		{
			case self::ATTR_STRICT:
				$this->_attributes[$attr] = (bool)$value;
				break;

			case self::ATTR_HANDLER:
				if (!\is_object($value)) {
					$this->_handleError('Failed to set config handler. The handler must be an object');
					$ret = false;
				} elseif (!($value instanceof \SiTech\ConfigParser\Handler\HandlerInterface)) {
					$this->_handleError('Failed to set config handler. The handler must implement SiTech_ConfigParser_Handler_Interface');
					$ret = false;
				} else {
					$this->_attributes[$attr] = $value;
				}
				break;

			case self::ATTR_ERRMODE:
				switch ($value) {
					case self::ERRMODE_EXCEPTION:
					case self::ERRMODE_SILENT:
					case self::ERRMODE_WARNING:
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
	 * Write the current configuration to a single specified item.
	 *
	 * @param string $item
	 * @return bool
	 */
	public function write($item)
	{
		return($this->_attributes[self::ATTR_HANDLER]->write($item, $this->_config));
	}

	/**
	 * Handle an error message based on the current ATTR_ERRMODE level.
	 *
	 * @param string $string
	 * @param array $array
	 */
	protected function _handleError($string, array $array = array())
	{
		if ($this->getAttribute(self::ATTR_ERRMODE) === self::ERRMODE_EXCEPTION) {
			throw new Exception($string, $array);
		} elseif ($this->getAttribute(self::ATTR_ERRMODE) === self::ERRMODE_WARNING) {
			\trigger_error(\vsprintf($string, $array), \E_USER_WARNING);
		}

		$this->_error = \vsprintf($string, $array);
	}
}
