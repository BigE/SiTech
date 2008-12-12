<?php
/**
 * Contains the driver for MySQL database connections.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_DB
 * @subpackage SiTech_DB_Driver
 * @version $Id: MySQL.php 146 2008-12-03 07:33:49Z eric $
 */

/**
 * @see SiTech_DB_Driver_Abstract
 */
require_once('SiTech/DB/Driver/Abstract.php');

/**
 * SiTech_DB_Driver_MySQL - For use with MySQL databases.
 *
 * Driver that contains special methods and instructions for MySQL database
 * connections.
 *
 * @package SiTech_DB
 * @subpackage SiTech_DB_Driver
 */
class SiTech_DB_Driver_MySQL extends SiTech_DB_Driver_Abstract
{
	public function getPrivileges($user=null, $host=null)
	{
		require_once('SiTech/DB/Privilege/MySQL.php');
		return(new SiTech_DB_Privilege_MySQL($this->pdo));
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
