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
 */

namespace SiTech\ConfigParser\Handler;

// Define the handler
const HANDLER_XML = 'SiTech\ConfigParser\Handler\XML';

/**
 * @see SiTech_ConfigParser_Handler_Interface
 */
require_once('SiTech/ConfigParser/Handler/IHandler.php');

/**
 * SiTech\ConfigParser\Handler\XML - Reads and writes configuration files that
 * are in XML format.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group © 2008-2011
 * @filesource
 * @package SiTech_ConfigParser
 * @subpackage SiTech_ConfigParser_Handler
 * @version $Id$
 */
class XML implements IHandler
{
	private $_depth = 0;

	private $_name = null;

	private $_buffer = array();

	private $_config = array();

	/**
	 * Read the specified file(s) into the configuration. Return value
	 * will be an array in filename => bool format.
	 *
	 * @param string $file File to read into configuration.
	 * @return array
	 */
	public function read($file)
	{
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($parser, array($this, '_startTag'), array($this, '_endTag'));
		xml_set_character_data_handler($parser, array($this, '_charData'));
        $fp = fopen($file, 'r');
		if (is_resource($fp)) {
			$ret[$file] = true;
			while (!feof($fp)) {
				$data = fread($fp, 4096);
				if (!xml_parse($parser, $data, feof($fp))) {
					$errno = xml_get_error_code($parser);
					$error = xml_error_string($errno);
					$lineno = xml_get_current_line_number($parser);
					$column = xml_get_current_column_number($parser);
					return(array(false, 'XML Parse Error: ('.$errno.') '.$error.' at line '.$lineno.' column '.$column));
				}
			}

			@fclose($fp);
			xml_parser_free($parser);
		} else {
			return(array(false, 'Failed to open file "'.$file.'" for reading'));
		}

		return(array(true, $this->_config));
	}

	/**
	 * Write the current configuration to a single specified file.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function write($file, $config)
	{
        $fp = @fopen($file, 'w');
		if (!is_resource($fp)) {
			return(array(false, 'Failed to open config file "%s" for writing', array($file)));
		}

		@fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<config>\n");

		foreach ($config as $section => $options) {
			@fwrite($fp, "\t<$section>\n");

			foreach ($options as $option => $val) {
				@fwrite($fp, "\t\t<$option>\n");
				if (is_array($val)) {
					$this->_walkArray($val, 3, $fp);
				} elseif (is_object($val)) {
					$val = serialize($val);
					@fwrite($fp, "\t\t\t$val\n");
				} else {
					@fwrite($fp, "\t\t\t$val\n");
				}

				@fwrite($fp, "\t\t</$option>\n");
			}

			@fwrite($fp, "\t</$section>\n");
		}

		@fwrite($fp, '</config>');
		@fclose($fp);

		return(true);
	}

	/**
	 * Parse character data from the XML.
	 *
	 * @param resource $parser
	 * @param string $data
	 */
	private function _charData($parser, $data)
	{
		$data = trim($data);

		if ($this->_depth < 3 || strlen($data) == 0) {
			return;
		}

		$var =& $this->_config;

		for ($i = 1; $i <= sizeof($this->_buffer); $i++) {
			$var =& $var[$this->_buffer[$i]];
		}

		$var = $data;
	}

	private function _endTag($parser, $name)
	{
		$this->_depth--;
		unset($this->_buffer[$this->_depth]);
	}

	private function _startTag($parser, $name, $attrs)
	{
		switch ($this->_depth) {
			case 0:
				/* First section ... increase depth but ignore it */
				$this->_depth++;
				return;

			case 1:
				/* This is a section */
				$this->_config[$name] = array();
				break;

			case 2:
				/* This is an option */
				$this->_config[$this->_buffer[1]][$name] = null;
				break;

			default:
				$var = &$this->_config;

				for ($i = 1; $i <= sizeof($this->_buffer); $i++) {
					$var =& $var[$this->_buffer[$i]];
				}

				if (isset($attrs['key'])) {
					$name = $attrs['key'];

					if (!is_array($var)) {
						$var = array();
					}

					if (is_numeric($name)) {
						$name = (int)$name;
					}

					$var[$name] = null;
				} else {
					$var = array();
				}
				break;
		}

		$this->_buffer[$this->_depth++] = $name;
	}

	private function _walkArray($array, $depth, $fp)
	{
		foreach ($array as $key => $val) {
			@fwrite($fp, str_repeat("\t", $depth)."<item key=\"$key\">\n");

			if (is_array($val)) {
				$this->_walkArray($val, $depth + 1, $fp);
			} else {
				@fwrite($fp, str_repeat("\t", $depth + 1)."$val\n");
			}

			@fwrite($fp, str_repeat("\t", $depth)."</item>\n");
		}
	}
}
