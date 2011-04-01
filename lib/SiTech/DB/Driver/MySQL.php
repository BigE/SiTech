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

namespace SiTech\DB\Driver;

const MYSQL = 'SiTech\DB\Driver\MySQL';

/**
 * @see SiTech\DB\Driver\Base
 */
require_once('SiTech/DB/Driver/Base.php');

/**
 * Driver that contains special methods and instructions for MySQL database
 * connections.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\DB
 * @subpackage SiTech\DB\Driver
 * @version $Id$
 */
class MySQL extends Base
{
	public function getPrivileges($user=null, $host=null)
	{
		require_once('SiTech/DB/Privilege/MySQL.php');
		return(new \SiTech\DB\Privilege\MySQL($this->pdo));
	}

	/**
	 * Singleton method to get the instance of the driver.

	 * @param SiTech_DB $pdo
	 * @return SiTech_DB_Driver_MySQL
	 */
	static public function singleton($pdo)
	{
		return(self::_singleton($pdo, __CLASS__));
	}
}
