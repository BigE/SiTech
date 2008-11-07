<?php
/**
 * Contains the driver for SQLite database connections.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
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
	 * @return SiTech_DB_Driver_SQLite
	 */
	static public function singleton($pdo)
	{
		return(self::_singleton($pdo, __CLASS__));
	}
}
