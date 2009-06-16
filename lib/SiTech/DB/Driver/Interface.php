<?php
/**
 * SiTech/DB/Driver/Interface.php
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
 * SiTech_DB_Driver_Interface - Interface for all database type classes.
 *
 * @package SiTech_DB
 * @subpackage SiTech_DB_Driver
 */
interface SiTech_DB_Driver_Interface
{
	/**
	 * Singleton method to get the instance of the driver.
	 *
	 * @param SiTech_DB $pdo
	 * @return SiTech_DB_Driver_Interface
	 */
	static public function singleton($pdo);
}
