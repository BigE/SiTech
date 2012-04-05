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
 *
 * @filesource
 */

namespace SiTech;

 /**
  * This class extends the base Exception class to add minor improvments to make
  * it easier to use. This means that exceptions will now take a formatted string
  * just like printf/sprintf will.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech
 * @version $Id$
 * @see http://php.net/exception
 */
class Exception extends \Exception
{
	/**
	 * This constructor takes a formatted string and arguments. It uses vsprintf
	 * to put the string together and pass to the internal Exception class.
	 *
	 * @param string $msg The exception message string. Please see http://php.net/printf
	 *                    for documentation of formatting.
	 * @param array $args Array of arguments to pass for formatting, like sprintf.
	 * @param int $code Error code to pass to internal Exception class.
	 * @param \Exception $previous Previous exception encountered in the stack.
	 */
	public function __construct($msg, $args = array(), $code = 0, \Exception $previous = null)
	{
		parent::__construct(vsprintf($msg, $args), $code, $previous);
	}
}

/**
 * Exception for features that are not implemented yet.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech
 * @version $Id$
 */
class NotImplementedException extends Exception {}

/**
 * Exception for features that are depricated and no longer in use.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech
 * @version $Id$
 */
class DepricatedException extends Exception {}
