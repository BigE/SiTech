<?php
/**
 * SiTech/DB/Driver/SQLite.php
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
 * @package SiTech_DB
 * @subpackage SiTech_DB_Driver
 * @version $Id$
 */

/**
 * @see SiTech_DB_Driver_Abstract
 */
require_once('SiTech/DB/Driver/Abstract.php');

/**
 * SiTech_DB_Driver_SQLite - For use with SQLite databases.
 *
 * Driver that contains special methods and instructions for SQLite database
 * connections.
 *
 * @package SiTech_DB
 * @subpackage SiTech_DB_Driver
 */
class SiTech_DB_Driver_SQLite extends SiTech_DB_Driver_Abstract
{
	/**
	 * Singleton method to get the instance of the driver.
	 *
	 * @param SiTech_DB $pdo
	 * @return SiTech_DB_Driver_SQLite
	 */
	static public function singleton($pdo)
	{
		return(self::_singleton($pdo, __CLASS__));
	}
}
