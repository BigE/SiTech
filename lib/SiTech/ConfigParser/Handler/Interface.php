<?php
/**
 * Contains the interface for all config handlers.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_ConfigParser
 * @subpackage SiTech_ConfigParser_Handler
 * @version $Id: Interface.php 128 2008-11-08 19:16:08Z eric $
 */

/**
 * SiTech_ConfigParser_Handler_Interface - Interface for all configuration
 * parser classes.
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
	 * @param string $file File name to read into configuration.
	 * @return array
	 */
	public function read($file);

	/**
	 * Write the current configuration to a single specified file.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function write($item, $config);
}
