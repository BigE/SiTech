<?php
require_once('SiTech.php');
SiTech::loadClass('SiTech_ConfigParser_Base');

/**
 * This class supports the XML extension for PHP for configuration file parsing
 * in XML format. If you would like to use SimpleXML instead of the XML extension
 * please use SiTech_ConfigParser_SimpleXML.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_ConfigParser_XML
 * @package SiTech_ConfigParser
 */
class SiTech_ConfigParser_XML extends SiTech_ConfigParser_Base
{
	private $_depth = 0;

	private $_name = null;

	private $_buffer = array();

	public function _charData($parser, $data)
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

	public function _endTag($parser, $name)
	{
		$this->_depth--;
		unset($this->_buffer[$this->_depth]);
	}

	/**
	 * Read the XML based configuration file into the config.
	 *
	 * @access  protected
	 * @param   string  Name of file to be read in
	 */
	protected function _read($file)
	{
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($parser, array($this, '_startTag'), array($this, '_endTag'));
		xml_set_character_data_handler($parser, array($this, '_charData'));
		if (($fp = fopen($file, 'r')) === false) {
			/* exception */
		}

		while ($data = fread($fp, 4096)) {
			if (!xml_parse($parser, $data, feof($fp))) {
				$errno = xml_get_error_code($parser);
				$error = xml_error_string($errno);
				$lineno = xml_get_current_line_number($parser);
				SiTech::loadClass('SiTech_ConfigParser_Exception');
				throw new SiTech_ConfigParser_Exception('XML error: (%d) %s at line %d', array($errno, $error, $lineno));
			}
		}

		fclose($fp);
		xml_parser_free($parser);
	}

	public function _startTag($parser, $name, $attrs)
	{
		switch ($this->_depth) {
			case 0:
				$this->_depth++;
				return;
				break;

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

	/**
	 */
	protected function _write($file)
	{
		if (($fp = fopen($file, 'w')) === false) {
		}

		fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<config>\n");
		foreach ($this->_config as $section => $options)
		{
			fwrite($fp, "\t<$section>\n");

			foreach ($options as $key => $val)
			{
				fwrite($fp, "\t\t<$key>\n");
				if (is_array($val)) {
					$this->_walkArray($val, 3, $fp);
				} else {
					fwrite($fp, "\t\t\t$val\n");
				}
				fwrite($fp, "\t\t</$key>\n");
			}

			fwrite($fp, "\t</$section>\n");
		}

		fwrite($fp, '</config>');
		fclose($fp);
	}

	private function _walkArray($array, $depth, $fp)
	{
		foreach ($array as $key => $val) {
			fwrite($fp, str_repeat("\t", $depth)."<item key=\"$key\">\n");
			if (is_array($val)) {
				$this->_walkArray($val, $depth + 1, $fp);
			} else {
				fwrite($fp, str_repeat("\t", $depth + 1)."$val\n");
			}
			fwrite($fp, str_repeat("\t", $depth)."</item>\n");
		}
	}
}
?>
