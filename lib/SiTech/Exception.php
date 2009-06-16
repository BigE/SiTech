<?php
/**
 * SiTech/Exception.php
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
 * @subpackage SiTech_Exception
 * @version $Id$
 */

 /**
  * SiTech_Exception
  *
  * This class extends the base Exception class to add minor improvments to make
  * it easier to use. This means that exceptions will now take a formatted string
  * just like printf/sprintf will.
  *
  * @package SiTech_Exception
  */
class SiTech_Exception extends Exception
{
	public function __construct($msg, $args = array(), $code = 0)
	{
		parent::__construct(vsprintf($msg, $args), $code);
	}
}
