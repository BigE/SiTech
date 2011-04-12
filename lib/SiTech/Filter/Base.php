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

namespace SiTech\Filter;

/**
 * @see SiTech\Filter\Exception
 */
require_once('SiTech/Filter/Exception.php');

/**
 * Input type for POST when using the filter.
 */
const INPUT_POST = 0;

/**
 * Input type for GET or the query string when using the filter.
 */
const INPUT_GET = 1;

/**
 * Input type for cookies when using the filter.
 */
const INPUT_COOKIE = 2;

/**
 * Input type for environment variables using the filter.
 */
const INPUT_ENV = 4;

/**
 * Input type for the server generated variables using the filter.
 */
const INPUT_SERVER = 5;

/**
 * Input type for any session variables using the filter.
 */
const INPUT_SESSION = 6;

/**
 * Input type to pull from $_REQUEST using the filter.
 */
const INPUT_REQUEST = 99;


////////////////////////////////////////////////////////////////////////////////
//////////////////////////////// Filter flags. /////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/**
 * No flags.
 */
const FLAG_NONE = 0;

/**
 * Allow octal notation (0[0-7]+) in "int" filter.
 */
const FLAG_ALLOW_OCTAL = 1;

/**
 * Allow hex notation (0x[0-9a-fA-F]+) in "int" filter.
 */
const FLAG_ALLOW_HEX = 2;

/**
 * Strip characters with ASCII value less than 32.
 */
const FLAG_STRIP_LOW = 4;

/**
 * Strip characters with ASCII value greater than 127.
 */
const FLAG_STRIP_HIGH = 8;

/**
 * Encode characters with ASCII value less than 32.
 */
const FLAG_ENCODE_LOW = 16;

/**
 * Encode characters with ASCII value greater than 127.
 */
const FLAG_ENCODE_HIGH = 32;

/**
 * Encode &.
 */
const FLAG_ENCODE_AMP = 64;

/**
 * Don't encode ' and ".
 */
const FLAG_NO_ENCODE_QUOTES = 128;

/**
 * (No use for now.)
 */
const FLAG_EMPTY_STRING_NULL = 256;

/**
 * Strip backticks.
 */
const FLAG_STRIP_BACKTICK = 512;

/**
 * Allow fractional part in "number_float" filter.
 */
const FLAG_ALLOW_FRACTION = 4096;

/**
 * Allow thousand separator (,) in "number_float" filter.
 */
const FLAG_ALLOW_THOUSAND = 8192;

/**
 * Allow scientific notation (e, E) in "number_float" filter.
 */
const FLAG_ALLOW_SCIENTIFIC = 16384;

/**
 * Require scheme in "validate_url" filter.
 */
const FLAG_SCHEME_REQUIRED = 65536;

/**
 * Require host in "validate_url" filter.
 */
const FLAG_HOST_REQUIRED = 131072;

/**
 * Require path in "validate_url" filter.
 */
const FLAG_PATH_REQUIRED = 262144;

/**
 * Require query in "validate_url" filter.
 */
const FLAG_QUERY_REQUIRED = 524288;

/**
 * Allow only IPv4 address in "validate_ip" filter.
 */
const FLAG_IPV4 = 1048576;

/**
 * Allow only IPv6 address in "validate_ip" filter.
 */
const FLAG_IPV6 = 2097152;

/**
 * Deny reserved addresses in "validate_ip" filter.
 */
const FLAG_NO_RES_RANGE = 4194304;

/**
 * Deny private addresses in "validate_ip" filter.
 */
const FLAG_NO_PRIV_RANGE = 8388608;

/**
 * Flag used to require scalar as input.
 */
const REQUIRE_SCALAR = 33554432;

/**
 * Require an array as input.
 */
const REQUIRE_ARRAY = 16777216;

/**
 * Always return an array.
 */
const FORCE_ARRAY = 67108864;

/**
 * Use NULL instead of FALSE on failure.
 */
const NULL_ON_FAILURE = 134217728;


////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// Filter types ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


// Validate Filters

/**
 * Validate int filter.
 */
const VALIDATE_INT = 257;

/**
 * Validate bool filter.
 */
const VALIDATE_BOOLEAN = 258;

/**
 * Validate float filter.
 */
const VALIDATE_FLOAT = 259;

/**
 * Validate regex filter.
 */
const VALIDATE_REGEXP = 272;

/**
 * Validate url filter.
 */
const VALIDATE_URL = 273;

/**
 * Validate e-mail filter.
 */
const VALIDATE_EMAIL = 274;

/**
 * Validate IP filter.
 */
const VALIDATE_IP = 275;


// These are the sanitize filters

/**
 * Unsafe/raw filter (also default filter)
 */
const UNSAFE_RAW = 516;

/**
 * String filter.
 */
const SANITIZE_STRING = 513;

/**
 * Stripped filter.
 */
const SANITIZE_STRIPPED = 513;

/**
 * Encoded filter.
 */
const SANITIZE_ENCODED = 514;

/**
 * Special chars filter.
 */
const SANITIZE_SPECIAL_CHARS = 515;

/**
 * 
 */
const SANITIZE_FULL_SPECIAL_CHARS = 515;

/**
 * E-mail filter.
 */
const SANITIZE_EMAIL = 517;

/**
 * URL filter.
 */
const SANITIZE_URL = 518;
 
/**
 * Number int filter.
 */
const SANITIZE_NUMBER_INT = 519;

/**
 * Number float filter.
 */
const SANITIZE_NUMBER_FLOAT = 520;

/**
 * Magic Quotes filter.
 */
const SANITIZE_MAGIC_QUOTES = 521;


// Other filters

/**
 * This is an attribute for the class. This can be used to set a default filter
 * for any values passed in.
 */
const FILTER_DEFAULT = 516;

/**
 * Callback filter. When using this filter, you must provide a callback method
 * to handle the data.
 */
const CALLBACK = 1024;


/**
 * This is the base of the filter. Here we define methods and values that can
 * be used in all types of the filter. It is not an abstract class because it
 * can be used by itself for basic filtering.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Filter
 * @version $Id$
 */
class Base
{
	/**
	 * Attributes set to the filter class. Only value that is set is the default
	 * filter which is set to UNSAFE_RAW
	 *
	 * @var array
	 */
	protected $_attributes = array(FILTER_DEFAULT => UNSAFE_RAW);

	/**
	 * One of the SiTech\Filter constants for the filter type we are using.
	 *
	 * @var int
	 */
	protected $_type;

	/**
	 * Setup the filter and get everything ready to do what we need.
	 *
	 * @param type $input Input type to use for the filter. If the type specified
	 *                    is not valid, an exception will be thrown.
	 */
	public function __construct($input = INPUT_POST, array $options = array())
	{
		$this->_filterExt = extension_loaded('filter');
		if (!in_array($input, array(INPUT_POST, INPUT_GET, INPUT_COOKIE, INPUT_ENV, INPUT_SERVER, INPUT_SESSION, INPUT_REQUEST))) {
			throw new Exception('The filter type %d specified is not a valid filter type', array($input));
		}
		$this->_type = (int)$input;
		foreach ($options as $k => $v) {
			$this->setAttribute($k, $v);
		}
	}

	/**
	 * Get the value of an attribute set with the instance of the filter.
	 *
	 * @param int $attribute Attribute to get the value of.
	 * @return mixed If the attribute is not set, null is returned.
	 */
	public function getAttribute($attribute)
	{
		return((isset($this->_attributes[$attribute]))? $this->_attributes[$attribute] : null);
	}
	
	/**
	 * Check to see if the filter has the variable specified.
	 *
	 * @param string $name Name of value to check if it exists.
	 * @return bool Returns true or false respectively.
	 */
	public function hasVar($name)
	{
		if ($this->_filterExt) {
			// So much easier if we have the filter extension...
			return(filter_has_var($this->_type, $name));
		} else {
			switch ($this->_type) {
				case INPUT_POST:
					return((isset($_POST[$name]))? true : false);

				case INPUT_GET:
					return((isset($_GET[$name]))? true : false);

				case INPUT_COOKIE:
					return((isset($_COOKIE[$name]))? true : false);

				case INPUT_ENV:
					return((isset($_ENV[$name]))? true : false);

				case INPUT_SERVER:
					return((isset($_SERVER[$name]))? true : false);

				case INPUT_SESSION:
					return((isset($_SESSION[$name]))? true : false);

				case INPUT_REQUEST:
					return((isset($_REQUEST[$name]))? true : false);
			}
		}
	}

	/**
	 * Depending on the INPUT_ specified when the class is created we pull the
	 * value from the superglobal here, then run it through the filter specified
	 * and return the value. This method can take an array instead of a single
	 * name.
	 *
	 * @param string $variable_name Name of the value to find from the input. If
	 *                              the variable name is an array then multiple
	 *                              values are parsed and returned as an array.
	 *                              See http://php.net/filter-input-array as an
	 *                              example
	 * @param int $filter Type of filter to use on the value before returning
	 * @param mixed $options Options to use for the filter
	 * @return mixed Returns false on failure and null if the value is not set
	 * @see http://php.net/filter-input-array
	 */
	public function input($variable_name, $filter = null, $options = FLAG_NONE)
	{
		if (empty($filter)) $filter = $this->getAttribute(FILTER_DEFAULT);
		if ($this->_filterExt) {
			if (is_array($variable_name)) {
				return(filter_input_array($this->_type, $variable_name));
			} else {
				return(filter_input($this->_type, $variable_name, $filter, $options));
			}
		} else {
			throw new \SiTech\NotImplementedException('\SiTech\Filter cannot operate without the filter extension yet.');
		}
	}

	/**
	 * Set attributes on this instance of the filter.
	 *
	 * @param int $attribute Attribute to set the value for.
	 * @param mixed $value Value to set attribute to. This varies by attribute.
	 */
	public function setAttribute($attribute, $value)
	{
		$this->_attributes[$attribute] = $value;
	}

	/**
	 * Filter a specific variable and return the value.
	 *
	 * @param mixed $variable Variable to pass through the specified filter
	 * @param int $filter Filter type to use on the value before returning
	 * @param mixed $options Options to pass into filter
	 * @return mixed Returns false on failure
	 */
	public function variable($variable, $filter = null, $options = FLAG_NONE)
	{
		if (empty($filter)) $filter = $this->getAttribute(FILTER_DEFAULT);
		if ($this->_filterExt) {
			return(filter_var($variable, $filter, $options));
		} else {
			throw new \SiTech\NotImplementedException('\SiTech\Filter cannot operate without the filter extension yet.');
		}
	}
}
