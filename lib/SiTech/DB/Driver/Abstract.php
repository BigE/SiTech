<?php
/**
 * SiTech/DB/Driver/Abstract.php
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
 * @see SiTech_DB_Driver_Interface
 */
require_once('SiTech/DB/Driver/Interface.php');

/**
 * SiTech_DB_Driver_Abstract - Base class for all database types.
 *
 * @package SiTech_DB
 * @subpackage SiTech_DB_Driver
 */
abstract class SiTech_DB_Driver_Abstract implements SiTech_DB_Driver_Interface
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
		if (empty(self::$instance)) {
			self::$instance = new $class($pdo);
		}

		return(self::$instance);
	}
}
