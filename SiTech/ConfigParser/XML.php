<?php
/**
 * @see SiTech_ConfigParser_Base
 */
require_once('SiTech/ConfigParser/Base.php');

class SiTech_ConfigParser_XML extends SiTech_ConfigParser_Base
{
	private $_depth = 0;
	
	private $_name = null;
	
	private $_buffer = array();
	
	/**
	 * Read the specified file(s) into the configuration. Return value
	 * will be an array in filename => bool format.
	 *
	 * @param array $files Files to read into configuration.
	 * @return array
	 */
	public function read(array $files)
	{
		$ret = array();
		
		foreach ($files as $file) {
			$parser = xml_parser_create();
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
			xml_set_element_handler($parser, array($this, '_startTag'), array($this, '_endTag'));
			xml_set_character_data_handler($parser, array($this, '_charData'));
			if (($fp = fopen($file, 'r')) !== false) {
				$ret[$file] = true;
				while (!feof($fp)) {
					$data = fread($fp, 4096);
					if (!xml_parse($parser, $data, feof($fp))) {
						$errno = xml_get_error_code($parser);
						$error = xml_error_string($errno);
						$lineno = xml_get_current_line_number($parser);
						$column = xml_get_current_column_number($parser);
						$this->_handleError('XML Parse Error: (%d) %s at line %d column %d', array($errno, $error, $lineno, $column));
						$ret[$file] = false;
						break;
					}
				}
				
				@fclose($fp);
				xml_parser_free($parser);
			} else {
				$this->_handleError('Failed to open file "%s" for reading', array($file));
				$ret[$file] = false;
			}
		}
		
		return($ret);
	}
	
	/**
	 * Write the current configuration to a single specified file.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function write($file)
	{
		if (($fp = @fopen($file, 'w')) === false) {
			$this->_handleError('Failed to open config file "%s" for writing', array($file));
			return(false);
		}
		
		@fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<config>\n");
		
		foreach ($this->_config as $section => $options) {
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
			
			@fwrite($fp, "\t<$section>\n");
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
