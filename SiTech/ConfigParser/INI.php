<?php
require_once('SiTech.php');
SiTech::loadClass('SiTech_ConfigParser_Base');

class SiTech_ConfigParser_INI extends SiTech_ConfigParser_Base
{
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
			if (file_exists($file)) {
				$config = parse_ini_file($file, true);
				$this->_config = array_merge($config, $this->_config);
				$ret[$file] = true;
			} else {
				$this->_handleError('Unable to find file "%s" for parsing', array($file));
				$ret[$file] = false;
			}
		}
		
		/* now loop through the config options and unserialize items */
		foreach ($this->_config as $section => $options) {
			foreach ($options as $option => $value) {
				if (preg_match('^a:\d+:{|^o:\d+:&quot;', $value)) {
					$this->_config = unserialize(str_replace('&quot;', '"', $value));
				}
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
?>