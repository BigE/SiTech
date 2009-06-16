<?php
/**
 * SiTech/Filter.php
 *
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
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2009
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Filter
 * @version $Id$
 */

/**
 * SiTech_Filter
 *
 * The filter class is a wrapper for the filter extension. This approach makes
 * the filter extension more object oriented.
 *
 * @package SiTech_Filter
 */
class SiTech_Filter
{
	const INPUT_POST = 0;
	const INPUT_GET = 1;
	const INPUT_COOKIE = 2;
	const INPUT_ENV = 4;
	const INPUT_SERVER = 5;
	const INPUT_SESSION = 6;
	const INPUT_REQUEST = 99;
	const FILTER_FLAG_NONE = 0;
	const FILTER_REQUIRE_SCALAR = 33554432;
	const FILTER_REQUIRE_ARRAY = 16777216;
	const FILTER_FORCE_ARRAY = 67108864;
	const FILTER_NULL_ON_FAILURE = 134217728;
	const FILTER_VALIDATE_INT = 257;
	const FILTER_VALIDATE_BOOLEAN = 258;
	const FILTER_VALIDATE_FLOAT = 259;
	const FILTER_VALIDATE_REGEXP = 272;
	const FILTER_VALIDATE_URL = 273;
	const FILTER_VALIDATE_EMAIL = 274;
	const FILTER_VALIDATE_IP = 275;
	const FILTER_DEFAULT = 516;
	const FILTER_UNSAFE_RAW = 516;
	const FILTER_SANITIZE_STRING = 513;
	const FILTER_SANITIZE_STRIPPED = 513;
	const FILTER_SANITIZE_ENCODED = 514;
	const FILTER_SANITIZE_SPECIAL_CHARS = 515;
	const FILTER_SANITIZE_EMAIL = 517;
	const FILTER_SANITIZE_URL = 518;
	const FILTER_SANITIZE_NUMBER_INT = 519;
	const FILTER_SANITIZE_NUMBER_FLOAT = 520;
	const FILTER_SANITIZE_MAGIC_QUOTES = 521;
	const FILTER_CALLBACK = 1024;
	const FLAG_ALLOW_OCTAL = 1;
	const FLAG_ALLOW_HEX = 2;
	const FLAG_STRIP_LOW = 4;
	const FLAG_STRIP_HIGH = 8;
	const FLAG_ENCODE_LOW = 16;
	const FLAG_ENCODE_HIGH = 32;
	const FLAG_ENCODE_AMP = 64;
	const FLAG_NO_ENCODE_QUOTES = 128;
	const FLAG_EMPTY_STRING_NULL = 256;
	const FLAG_ALLOW_FRACTION = 4096;
	const FLAG_ALLOW_THOUSAND = 8192;
	const FLAG_ALLOW_SCIENTIFIC = 16384;
	const FLAG_SCHEME_REQUIRED = 65536;
	const FLAG_HOST_REQUIRED = 131072;
	const FLAG_PATH_REQUIRED = 262144;
	const FLAG_QUERY_REQUIRED = 524288;
	const FLAG_IPV4 = 1048576;
	const FLAG_IPV6 = 2097152;
	const FLAG_NO_RES_RANGE = 4194304;
	const FLAG_NO_PRIV_RANGE = 8388608;

	protected $input = null;

	/**
	 * Constructor. Here we check if we have the filter extension. If it is not
	 * loaded, we cannot continue.
	 *
	 * @param int $input Default input to use when using input or hasVar methods.
	 */
	public function __construct($input = null)
	{
		if (!extension_loaded('filter')) {
			throw new Exception('The filter extesion is required');
		}

		if ($input === 0 || !empty($input)) {
			$this->input = $input;
		} else {
			$this->input = self::INPUT_REQUEST;
		}
	}

	/**
	 */
	public function filter($value, $filter=null, $options=array())
	{
		if (empty($filter)) {
			return(filter_var($value));
		} elseif (empty($options)) {
			return(filter_var($value, $filter));
		} else {
			return(filter_var($value, $filter, $options));
		}
	}

	/**
	 * Check to see if a variable type exists.
	 *
	 * @param string $varName Variable name to check for.
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function hasVar($varName)
	{
		switch ($this->input) {
			case self::INPUT_REQUEST:
				return(isset($_REQUEST[$varName]));

			case self::INPUT_SESSION:
				return(isset($_SESSION[$varName]));

			default:
				return(filter_has_var($this->input, $varName));
		}
	}

	/**
	 * Get a list of all supported filters. An empty array is returned if there
	 * are no filters available.
	 *
	 * @return array Array of supported filters.
	 */
	public function listFilters()
	{
		return(filter_list());
	}

	/**
	 */
	public function input($varName, $filter=null, $options=array())
	{
		if ($filter == null) {
			switch ($this->input) {
				case self::INPUT_REQUEST:
                    $var = filter_input(self::INPUT_GET, $varName);
					if (empty($var)) {
						$var = filter_input(self::INPUT_POST, $varName);
					}

					return($var);

				case self::INPUT_SESSION:
					return(false);

				default:
					return(filter_input($this->input, $varName));
			}
		} elseif (!empty($options)) {
			switch ($this->input) {
				case self::INPUT_REQUEST:
					$var = filter_input(self::INPUT_GET, $varName, $filter, $options);
                    if (empty($var)) {
						$var = filter_input(self::INPUT_POST, $varName, $filter, $options);
					}

					return($var);

				case self::INPUT_SESSION:
					return(false);

				default:
					return(filter_input($this->input, $varName, $filter, $options));
			}
		} else {
			switch ($this->input) {
				case self::INPUT_REQUEST:
					$var = filter_input(self::INPUT_GET, $varName, $filter);
                    if (empty($var)) {
						$var = filter_input(self::INPUT_POST, $varName, $filter);
					}

					return($var);

				case self::INPUT_SESSION:
					return(false);

				default:
					return(filter_input($this->input, $varName, $filter));
			}
		}
	}

	public function requestMethod()
	{
		return(strtolower($_SERVER['REQUEST_METHOD']));
	}
}
