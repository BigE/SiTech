<?php
require_once('SiTech/ConfigParser/Handler/Interface.php');

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
			$ret = false;
		}

		/* now loop through the config options and unserialize items */
		foreach ($config as $section => $options) {
			foreach ($options as $option => $value) {
				if (preg_match('#^a:\d+:{|^o:\d+:&quot;#', $value)) {
					$this->_config[$section][$option] = unserialize(str_replace('&quot;', '"', $value));
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
	public function write($file)
	{
		if (is_writeable($file)) {
			if (($fp = @fopen($file, 'w')) !== false) {
				foreach ($this->_config as $section => $options) {
					@fwrite($fp, "[$section]\n");
					foreach ($options as $option => $value) {
						if (is_array($value) || is_object($value)) {
							$value = serialize($value);
						}

						$value = str_replace('"', '&quot;', $value);
						@fwrite($fp, "$option=$value\n");
					}
				}
				@fclose($fp);
			} else {
				$this->_handleError('Failed to open config file "%s" for writing', array($file));
			}
		} else {
			$this->_handleError('Configuration file "%s" is not writeable', array($file));
		}
	}
}
