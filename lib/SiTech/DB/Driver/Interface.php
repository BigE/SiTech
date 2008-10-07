<?php
/**
 * Contains the interface for all database drivers.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
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
	 * @return SiTech_DB_Driver_Interface
	 */
	static public function singleton($pdo);
}
