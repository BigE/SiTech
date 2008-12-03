<?php
/**
 * Contains the Array handler for the config parsers.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_ConfigParser
 * @subpackage SiTech_ConfigParser_Handler
 * @version $Id: Array.php 128 2008-11-08 19:16:08Z eric $
 */

/**
 * @see SiTech_ConfigParser_Handler_Interface
 */
require_once('SiTech/ConfigParser/Handler/Interface.php');

/**
 * SiTech_ConfigParser_Handler_Array - Reads and writes configuration files that
 * are in Array format.
 *
 * @package SiTech_ConfigParser
 * @subpackage SiTech_ConfigParser_Handler
 */
class SiTech_ConfigParser_Handler_Array implements SiTech_ConfigParser_Handler_Interface
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
			include($file);
            if (isset($config)) {
                $ret = true;

				foreach ($config as $section => &$options) {
					foreach ($options as $opt => &$val) {
						$val = $this->_readValue($opt, $val);
						if (substr($opt, 0, 12) === '_sitech_obj_') {
							$options[substr($opt, 12)] = $val;
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
		if ((file_exists($file) && is_writeable($file)) || (!file_exists($file) && is_writeable(dirname($file)))) {
			$fp = @fopen($file, 'w');
			if ($fp !== false) {
				@fwrite($fp, "<?php\n\$config = array(\n");
				foreach ($config as $section => $options) {
					@fwrite($fp, "\t".(is_numeric($section)? $section : '\''.addslashes($section).'\'')." => array(\n");
					foreach ($options as $option => $value) {
						if (is_object($value)) {
							$option = '_sitech_obj_'.$option;
						}
						@fwrite($fp, "\t\t".(is_numeric($option)? $option : '\''.addslashes($option).'\'').' => ');
						$this->_writeValue($fp, $value);
					}
					/* just to tidy it up and make it a bit cleaner. */
					@fseek($fp, -2, SEEK_END);
					@fwrite($fp, "\n\t),\n");
				}
				/* just to tidy it up and make it a bit cleaner. */
				@fseek($fp, -2, SEEK_END);
				@fwrite($fp, "\n);\n");
				@fclose($fp);
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
		if (is_string($value)) {
			$value = stripslashes($value);
		}
		
		if (substr($option, 0, 12) === '_sitech_obj_') {
			$value = unserialize($value);
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
	 * @todo Add object detection support.
	 */
	protected function _writeValue($fp, $value, $indent = 2)
	{
		if (is_array($value)) {
			@fwrite($fp, "array(\n");
			foreach ($value as $k => $v) {
				@fwrite($fp, str_repeat("\t", $indent + 1).(is_numeric($k)? $k : '\''.addslashes($k).'\'').' => ');
				$this->_writeValue($fp, $v, $indent + 1);
			}
			@fwrite($fp, str_repeat("\t", $indent)."),\n");
		} elseif (is_numeric($value)) {
			@fwrite($fp, "$value,\n");
		} else {
			if (is_object($value)) {
				$value = serialize($value);
			}
			/* assume string */
			$value = addslashes($value);
			@fwrite($fp, "'$value',\n");
		}
	}
}
