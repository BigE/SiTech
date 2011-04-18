<?php
/**
 * SiTech/ConfigParser/Handler/INI.php
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
 * @package SiTech_ConfigParser
 * @subpackage SiTech_ConfigParser_Handler
 * @version $Id$
 */

/**
 * @see SiTech_ConfigParser_Handler_Interface
 */
require_once('SiTech/ConfigParser/Handler/Interface.php');

/**
 * SiTech_ConfigParser_Handler_INI - Reads and writes configuration files that
 * are in INI format.
 *
 * @package SiTech_ConfigParser
 * @subpackage SiTech_ConfigParser_Handler
 */
class SiTech_ConfigParser_Handler_INI implements SiTech_ConfigParser_Handler_Interface
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
		if (file_exists($file)) {
			$config = parse_ini_file($file, true);
			$ret = true;
		} else {
			$config = array();
			$ret = false;
		}

		/* now loop through the config options and unserialize items */
		foreach ($config as $section => $options) {
			foreach ($options as $option => $value) {
				$config[$section][$option] = urldecode($value);
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
		if ((file_exists($file) && is_writeable($file)) || (!file_exists($file) && is_writeable(dirname($file)))) {
			$fp = @fopen($file, 'w');
			if ($fp !== false) {
				foreach ($config as $section => $options) {
					@fwrite($fp, "[$section]\n");
					foreach ($options as $option => $value) {
						if (is_array($value) || is_object($value)) {
							$value = serialize($value);
						}

						$value = urlencode($value);
						@fwrite($fp, "$option=$value\n");
					}
				}
				@fclose($fp);
			} else {
				return(array(false, 'Failed to open config file "'.$file.'" for writing'));
			}
		} else {
			return(array(false, 'Configuration file "'.$file.'" is not writeable'));
		}

		return(true);
	}
}
