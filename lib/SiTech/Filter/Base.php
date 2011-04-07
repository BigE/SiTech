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


/**
 * This is the base of the filter. Here we define methods and values that can
 * be used in all types of the filter.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Filter
 * @version $Id$
 */
abstract class Base
{
	protected $_type;

	public function __construct($input = INPUT_POST)
	{
		$this->_filterExt = extension_loaded('filter');
		$this->_type = $input;
	}
	
	public function hasVar($name)
	{
		if ($this->_filterExt) {
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
}
