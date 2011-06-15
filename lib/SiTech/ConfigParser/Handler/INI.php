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

namespace SiTech\ConfigParser\Handler;

// Define the handler
const HANDLER_INI = 'SiTech\ConfigParser\Handler\INI';

/**
 * @see SiTech_ConfigParser_Handler_Interface
 */
require_once('SiTech/ConfigParser/Handler/IHandler.php');

/**
 * Reads and writes configuration files that are in INI format.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\ConfigParser
 * @subpackage SiTech\ConfigParser\Handler
 * @version $Id$
 */
class INI implements IHandler
{
	/**
	 * Read the specified file(s) into the configuration. Return value
	 * will be an array in filename => bool format.
	 *
	 * @param array $files Files to read into configuration.
	 * @return array
	 */
	public function read($file)
	{
		if (\file_exists($file)) {
			$config = \parse_ini_file($file, true);
			$ret = true;
		} else {
			$config = array();
			$ret = false;
		}

		/* now loop through the config options and unserialize items */
		foreach ($config as $section => $options) {
			foreach ($options as $option => &$value) {
				if (is_string($value)) $value = \stripslashes($value);
				if (\substr($value, -2) === '==') {
					if (\base64_decode($value, true) !== false) {
						$value = \base64_decode($value);
					}
				}
			}
		}

		return(array($ret, $config));
	}

	/**
	 * Write the current configuration to a single specified file.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function write($file, $config)
	{
		if ((\file_exists($file) && \is_writeable($file)) || (!\file_exists($file) && \is_writeable(\dirname($file)))) {
			$fp = @\fopen($file, 'w');
			if ($fp !== false) {
				foreach ($config as $section => $options) {
					@\fwrite($fp, "[$section]\n");
					foreach ($options as $option => $value) {
						if (\is_array($value) || \is_object($value)) {
							$value = \base64_encode(\serialize($value));
						}

						$value = addslashes($value);
						@\fwrite($fp, "$option=\"$value\"\n");
					}
				}
				@\fclose($fp);
			} else {
				return(array(false, 'Failed to open config file "'.$file.'" for writing'));
			}
		} else {
			return(array(false, 'Configuration file "'.$file.'" is not writeable'));
		}

		return(true);
	}
}
