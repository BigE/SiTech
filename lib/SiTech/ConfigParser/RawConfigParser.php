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

namespace SiTech\ConfigParser;

/**
 * @see SiTech\ConfigParser\Exception
 */
require_once('SiTech/ConfigParser/Exception.php');

/**
 * This configuration class was closely modeled after the ConfigParser module
 * that is found in Python. I found it to be a very useful class and decided to
 * port the functionality over to this library.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\ConfigParser
 * @version $Id$
 */
class RawConfigParser
{
	/**
	 * To enable strict mode set this attribute to true. This will cause an
	 * exception to be thrown if a config value is being set that is already
	 * set.
	 */
	const ATTR_STRICT = 0;

	/**
	 * This used to control the output mode of errors thrown through the config
	 * parser. Now all errors are exceptions and must be caught.
	 *
	 * @depricated All errors are now handled through specific exceptions
	 */
	const ATTR_ERRMODE = 1;

	/**
	 * This attribute is the backend handler for the config parser. The default
	 * is the INI handler. To use a different one, initate an instance of the
	 * handler and pass the object as the attribute. The instance must implement
	 * SiTech\ConfigParser\Handler\IHandler
	 *
	 * @see SiTech\ConfigParser\Handler\IHandler
	 */
	const ATTR_HANDLER = 2;

	/**
	 * This attribute is for the environment that the code is running in. It can
	 * be set directly or through the SITECH_ENV constant. This will cause the
	 * configuration to try to read sections as "$env:$section" but still
	 * default to reading "$section" if no "$env:$section" is found. The default
	 * environment is "production"
	 */
	const ATTR_ENV = 3;

	/**
	 * Array of attributes set on the configuration parser. The getAttribute and
	 * setAttribute methods controll this array.
	 *
	 * @var array
	 */
	protected $_attributes = array();

	/**
	 * This is an array of the configuration that is loaded into the config
	 * parser instance. It is populated by the handler and written to files
	 * by the handler.
	 *
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Constructor for config parser.
	 *
	 * @param array $options Array of self::ATTR_* options to set.
	 */
	public function __construct(array $options = array())
	{
		foreach ($options as $attr => $value) {
			$this->setAttribute($attr, $value);
		}

		if (empty($this->_attributes[self::ATTR_HANDLER])) {
			/* default to INI files */
			require_once('SiTech/ConfigParser/Handler/INI.php');
			$this->setAttribute(self::ATTR_HANDLER, new Handler\INI());
		}

		if (empty($this->_attributes[self::ATTR_ENV])) {
			if (defined('SITECH_ENV')) {
				$this->setAttribute(self::ATTR_ENV, SITECH_ENV);
			} else {
				$this->setAttribute(self::ATTR_ENV, 'production');
			}
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
	 * @throws DuplicateSectionException
	 */
	public function addSection($section)
	{
		if (($section = $this->hasSection($section)) !== false) {
			$this->_config[$section] = array();
			return(true);
		} else {
			throw new DuplicateSectionException('The section %s already exists.', array($section));
		}
	}

	/**
	 * Get the specified option value for the named section. This default
	 * method returns all values as what they are in the config.
	 *
	 * @param string $section Section name where option exists.
	 * @param string $option Option name to get value of.
	 * @return mixed
	 * @throws NoOptionException NoSectionException
	 */
	public function get($section, $option)
	{
		if (($section = $this->hasSection($section)) !== false) {
			if ($this->hasOption($section, $option)) {
				return($this->_config[$section][$option]);
			} else {
				throw new NoOptionException('Cannot retreive value for option "%s" because it does not exist', array($option));
			}
		} else {
			throw new NoSectionException('Cannot retreive value for option "%s" because section "%s" does not exist', array($option, $section));
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
		switch ($attr) {
			// This is a true/false value .. so we need to typecast as bool
			case self::ATTR_STRICT:
				$val = (isset($this->_attributes[$attr]))? (bool)$this->_attributes[$attr] : false;
				break;

			default:
				$val = (isset($this->_attributes[$attr]))? $this->_attributes[$attr] : null;
				break;
		}

		return($val);
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
		if (($section = $this->hasSection($section)) !== false && isset($this->_config[$section][$option])) {
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
		if (($env = $this->getAttribute(self::ATTR_ENV)) !== null)
			$env_section = $env.':'.$section;

		if (isset($env_section) && isset($this->_config[$env_section])) {
			return($env_section);
		} elseif (isset($this->_config[$section])) {
			return($section);
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
		if (($section = $this->hasSection($section)) !== false) {
			return($this->_config[$section]);
		} else {
			throw new NoSectionException('Cannot retreive items because section "%s" does not exist', array($section));
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

		if (($section = $this->hasSection($section)) !== false) {
			if (!empty($option)) {
				if ($this->hasOption($section, $option)) {
					if ($reference) {
						$GLOBALS[$option] =& $this->_config[$section][$option];
					} else {
						$GLOBALS[$option] = $this->_config[$section][$option];
					}
				} else {
					throw new NoOptionException('Cannot make option "%s" of section "%s" global because the option does not exist', array($option, $section));
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
				throw new NoSectionException('Cannot make section "%s" global because it does not exist', array($section));
			} else {
				throw new NoSectionException('Cannot make option "%s" of section "%s" global because the section does not exist', array($option, $section));
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
		if (($section = $this->hasSection($section)) !== false) {
			return(array_keys($this->_config[$section]));
		} else {
			throw new NoSectionException('Cannot retreive options because section "%s" does not exist', array($section));
		}
	}

	public function read(array $items)
	{
		$ret = array();

		foreach ($items as $item) {
			list($bool, $config) = $this->_attributes[self::ATTR_HANDLER]->read($item);
			$ret[$item] = $bool;
			if (!$bool) {
				throw new Exception('Unable to parse "%s" into the config', array($item));
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
		if (($section = $this->hasSection($section)) !== false) {
			if ($this->hasOption($section, $option)) {
				unset($this->_config[$section][$option]);
				return(true);
			} else {
				throw new NoOptionException('Cannot remove the option "%s" from section "%s" because it does not exist', array($option, $section));
			}
		} else {
			throw new NoSectionException('Cannot remove option "%s" from section "%s" because the section does not exist', array($option, $section));
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
		if (($section = $this->hasSection($section)) !== false) {
			unset($this->_config[$section]);
			return(true);
		} else {
			throw new NoSectionException('Cannot remove section "%s" because it does not exist', array($section));
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
		if (($section = $this->hasSection($section)) !== false) {
			if ($this->hasOption($section, $option) && $this->getAttribute(self::ATTR_STRICT) === true) {
				throw new Exception('Strict mode in effect - Cannot overwrite option "%s" in section "%s"', array($option, $section));
			}

			$this->_config[$section][$option] = $value;
			return(true);
		} else {
			throw new NoSectionException('Cannot set option "%s". No section named "%s" exists in configuration!', array($section, $option));
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
					throw new Exception('Failed to set config handler. The handler must be an object');
				} elseif (!($value instanceof \SiTech\ConfigParser\Handler\IHandler)) {
					throw new Exception('Failed to set config handler. The handler must implement SiTech\ConfigParser\Handler\IHandler');
				} else {
					$this->_attributes[$attr] = $value;
				}
				break;

			case self::ATTR_ERRMODE:
				/*switch ($value) {
					case self::ERRMODE_EXCEPTION:
					case self::ERRMODE_SILENT:
					case self::ERRMODE_WARNING:
						$this->_attributes[$attr] = $value;
						break;

					default:
						throw new Exception('Invalid error mode setting for %s::ATTR_ERRMODE', array(__CLASS__));
						break;
				}*/
				require_once('SiTech/Exception.php');
				throw new \SiTech\DepricatedException('%s::ATTR_ERRMODE is now depricated. All errors are now exceptions that are thrown.', array(__CLASS__));
				break;

			case self::ATTR_ENV:
				$this->_attributes[$attr] = $value;
				return;

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
}
