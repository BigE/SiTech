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

namespace SiTech\DB\Driver;

/**
 * @see SiTech\DB\Driver\Interface
 */
require_once('SiTech/DB/Driver/Interface.php');

/**
 * Base class for all database types.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2011
 * @filesource
 * @package SiTech\DB
 * @subpackage SiTech\DB\Driver
 * @version $Id$
 */
abstract class ADriver implements IDriver
{
	/**
	 * Instance of itself.
	 *
	 * @var SiTech_DB_Driver_Interface
	 */
	static protected $instance;

	/**
	 * Instance of SiTech_DB
	 *
	 * @var SiTech_DB
	 */
	protected $pdo;

	/**
	 * Constructor.
	 *
	 * @param SiTech_DB $pdo
	 */
	protected function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Get the instance of the class specified. The class that extends this
	 * class should have a singleton() method that will pass __CLASS__ to this
	 * protected method.
	 *
	 * @param SiTech_DB $pdo
	 * @param string $class Class name that we're getting an instance of.
	 * @return SiTech_DB_Driver_Interface
	 */
	final static protected function _singleton($pdo, $class)
	{
		if (empty(static::$instance)) {
			static::$instance = new $class($pdo);
		}

		return(static::$instance);
	}
}
