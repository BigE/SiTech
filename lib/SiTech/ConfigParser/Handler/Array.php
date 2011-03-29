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
const HANDLER_ARRAY = 'SiTech\ConfigParser\Handler\Array';

/**
 * @see SiTech\ConfigParser\Handler\IHandler
 */
require_once('SiTech/ConfigParser/Handler/IHandler.php');

/**
 * Reads and writes configuration files that are in Array format.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\ConfigParser
 * @subpackage SiTech\ConfigParser\Handler
 * @version $Id$
 */
class _Array implements IHandler
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
			include($file);
            if (isset($config)) {
                $ret = true;

				foreach ($config as $section => &$options) {
					foreach ($options as $opt => &$val) {
						$val = $this->_readValue($opt, $val);
						if (\substr($opt, 0, 12) === '_sitech_obj_') {
							$options[\substr($opt, 12)] = $val;
							unset($options[$opt]);
						}
					}
				}
            }
		}

        if (!isset($ret)) {
			$config = array();
			$ret = false;
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
				@\fwrite($fp, "<?php\n\$config = array(\n");
				foreach ($config as $section => $options) {
					@\fwrite($fp, "\t".(\is_numeric($section)? $section : '\''.\addslashes($section).'\'')." => array(\n");
					foreach ($options as $option => $value) {
						if (\is_object($value)) {
							$option = '_sitech_obj_'.$option;
						}
						@\fwrite($fp, "\t\t".(\is_numeric($option)? $option : '\''.\addslashes($option).'\'').' => ');
						$this->_writeValue($fp, $value);
					}
					/* just to tidy it up and make it a bit cleaner. */
					@\fseek($fp, -2, \SEEK_END);
					@\fwrite($fp, "\n\t),\n");
				}
				/* just to tidy it up and make it a bit cleaner. */
				@\fseek($fp, -2, \SEEK_END);
				@\fwrite($fp, "\n);\n");
				@\fclose($fp);
			} else {
				return(array(false, 'Failed to open config file "'.$file.'" for writing'));
			}
		} else {
			return(array(false, 'Configuration file "'.$file.'" is not writeable'));
		}

		return(true);
	}

	protected function _readValue($option, $value)
	{
		if (\is_string($value)) {
			$value = \stripslashes($value);
		}

		if (\substr($option, 0, 12) === '_sitech_obj_') {
			$value = \unserialize($value);
		}

		return($value);
	}

	/**
	 * This writes a single value to the config. It does some type checking for
	 * numbers and array values currently.
	 *
	 * @param resource $fp File pointer
	 * @param mixed $value Value to write to the config.
	 * @param int $indent Indentation length
	 */
	protected function _writeValue($fp, $value, $indent = 2)
	{
		if (\is_array($value)) {
			@\fwrite($fp, "array(\n");
			foreach ($value as $k => $v) {
				@\fwrite($fp, \str_repeat("\t", $indent + 1).(\is_numeric($k)? $k : '\''.\addslashes($k).'\'').' => ');
				$this->_writeValue($fp, $v, $indent + 1);
			}
			@\fwrite($fp, \str_repeat("\t", $indent)."),\n");
		} elseif (\is_numeric($value)) {
			@\fwrite($fp, "$value,\n");
		} else {
			if (\is_object($value)) {
				$value = \serialize($value);
			}
			/* assume string */
			$value = \addslashes($value);
			@\fwrite($fp, "'$value',\n");
		}
	}
}
