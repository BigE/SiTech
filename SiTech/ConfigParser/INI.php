<?php
/**
 * INI support for SiTech_ConfigParser
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_ConfigParser
 */

/**
 * SiTech include.
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_ConfigParser_Base');

/**
 * This class supports INI formatted configuration files. Base functionality
 * is just to read and write them.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_ConfigParser_XML
 * @package SiTech_ConfigParser
 */
class SiTech_ConfigParser_INI extends SiTech_ConfigParser_Base
{
	/**
	 * Read the INI based configuration file into the config.
	 *
	 * @access  protected
	 * @param   string  Name of file to be read in
	 */
	protected function _read($file)
	{
		/* parse it into sections because that's how our configuration is setup. */
		$this->_config = parse_ini_file($file, true);

		/* Now get our arrays back to a useable format */
		foreach ($this->_config as $section => $options) {
			foreach ($options as $opt => $val) {
				if (preg_match('#^a:[0-9]+:\{.*}$#', $val)) {
					$this->_config[$section][$opt] = unserialize(str_replace('\'', '"', $val));
				}
			}
		}
	}

	/**
	 */
	protected function _write($file)
	{
		if (($fp = fopen($file, 'w')) === false) {
			SiTech::loadClass('SiTech_ConfigParser_Exception');
			throw new SiTech_ConfigParser_Exception('Could not open configuration file %s for writing.', $file);
		}

		foreach ($this->_config as $section => $options)
		{
			fwrite($fp, "[$section]\n");

			foreach ($options as $key => $val)
			{
				if (is_array($val)) {
					$val = str_replace('"', '\'', serialize($val));
				}

				fwrite($fp, "$key = \"$val\"\n");
			}

			fwrite($fp, "\n");
		}

		fclose($fp);
	}
}
?>
