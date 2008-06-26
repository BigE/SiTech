<?php
/**
 * Contains the interface for all database drivers.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_ConfigParser
 * @subpackage SiTech_ConfigParser_Handler
 * @version $Id$
 */

/**
 * SiTech_DB_Driver_Interface - Interface for all database type classes.
 *
 * @package SiTech_ConfigParser
 * @subpackage SiTech_ConfigParser_Handler
 */
interface SiTech_ConfigParser_Handler_Interface
{
	/**
	 * Read the specified item(s)/file(s) into the configuration. Return value
	 * will be an array in array(bool, array(config)) format.
	 *
	 * @param array $files Files to read into configuration.
	 * @return array
	 */
	public function read($item);

	/**
	 * Write the current configuration to a single specified file.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function write($file);
}
