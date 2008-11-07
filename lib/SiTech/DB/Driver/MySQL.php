<?php
/**
 * Contains the driver for MySQL database connections.
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
	/**
	 * Singleton method to get the instance of the driver.
	 *
	 * @return SiTech_DB_Driver_MySQL
	 */
	static public function singleton()
	{
		return(self::_singleton(__CLASS__));
	}
}
