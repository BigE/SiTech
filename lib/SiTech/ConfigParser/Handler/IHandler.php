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

namespace SiTech\ConfigParser\Handler;

/**
 * Interface for all configuration parser classes.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\ConfigParser
 * @subpackage SiTech\ConfigParser\Handler
 * @version $Id$
 */
interface IHandler
{
	/**
	 * Read the specified item(s)/file(s) into the configuration. Return value
	 * will be an array in array(bool, array(config)) format.
	 *
	 * @param string $file File name to read into configuration.
	 * @return array
	 */
	public function read($file);

	/**
	 * Write the current configuration to a single specified file.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function write($item, $config);
}
