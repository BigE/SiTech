<?php
/**
 */

/**
 * Description of Plugins
 *
 * @author Eric Gach <eric at php-oop dot net>
 */
class SiTech_Plugins
{
	/**
	 * This setting is for the maximum filesize of a plugin. The number is
	 * represented in KB. (default: 2048)
	 */
	const ATTR_MAX_FILESIZE = 1;

	/**
	 * Settings for the plugins.
	 *
	 * @var array
	 */
	protected $_attributes = array();

	/**
	 * Plugins storage array.
	 *
	 * @var array
	 */
	protected $_plugins = array();

	/**
	 * Setup basic settings for the plugins.
	 */
	public function __construct()
	{
		$this->_attributes[self::ATTR_MAX_FILESIZE] = 2048;
	}

	/**
	 * Get an attribute setting.
	 *
	 * @param int $attr SiTech_Plugins::ATTR_* attribute to get.
	 * @return mixed
	 */
	public function getAttribute($attr)
	{
		return((isset($this->_attribute[$attr])? $this->_attribute[$attr] : null));
	}

	/**
	 * Load a plugin into the stack. If the plugin cannot be read, or fails to
	 * load an exception will be thrown.
	 *
	 * @param string $plugin
	 * @param string $file Filename of the plugin to load. This is stored
	 *                     internally for when the plugin is reloaded.
	 * @throws SiTech_Plugins_Exception
	 */
	public function load($plugin, $file)
	{
		$this->_parse($file);
	}

	/**
	 * Plugin that we need to reload. If the plugin is not loaded, or fails to
	 * reload, an exception will be thrown.
	 *
	 * @param string $plugin Plugin name to reload.
	 * @throws SiTech_Plugins_Exception
	 */
	public function reload($plugin)
	{
	}

	/**
	 * Set an attribute setting.
	 *
	 * @param int $attr SiTech_Plugins::ATTR_* attribute to set.
	 * @param mixed $value Value to set the attribute to.
	 */
	public function setAttribute($attr, $value)
	{
		$this->_attributes[$attr] = $value;
	}

	/**
	 * This unloads a plugin from the current stack. This will remove any local
	 * references to the class or functions created.
	 *
	 * @param string $plugin Plugin name to unload.
	 * @throws SiTech_Plugins_Exception
	 */
	public function unload($plugin)
	{
	}

	/**
	 * Parse the plugin file and return the needed data for the plugin to work
	 * completely. Remember when parsing, the plugin can be functions or classes.
	 *
	 * @param string $file Filename to load and parse up.
	 * @return ?
	 * @throws SiTech_Plugins_Exception
	 */
	protected function _parse($file)
	{

		if (filesize($file) > ($this->_attributes[self::ATTR_MAX_FILESIZE] * 1024)) {
			require_once('SiTech/Plugins/Exception.php');
			throw new SiTech_Plugins_Exception('The plugin file "%s" could not be loaded because it exceeds the maximum filesize of %d KB', array($file, $this->_attributes[self::ATTR_MAX_FILESIZE]));
		}
		
		if (!file_exists($file) || !is_readable($file) || ($contents = php_strip_whitespace($file)) === false) {
			require_once('SiTech/Plugins/Exception.php');
			throw new SiTech_Plugins_Exception('The plugin file "%s" could not be read');
		}

		$tokens = token_get_all($contents);
		/* 5.3 gc ftw! */
		unset($contents);

		/**
		 * Initalize the needed variables - then we get to loop through tokens
		 * and start the parsing!
		 */
		$token = current($tokens); /* get the first token */
		$in_class = false; /* If we're in a class, this will be set to true */
		$class_name = ''; /* This is the name of the class we're working with */

		/* Start thine loop o glory! */
		do {
			if ($in_class === false && $token[0] == T_CLASS) {
				/**
				 * OK, this tells us we've got a class. Now we need to set some
				 * variables so we know where we're at.
				 */
				$in_class = true;
			} elseif ($in_class === true && empty($class_name) && $token[0] == T_STRING) {
				/**
				 * The first "string" to come after a T_CLASS is the class
				 * name itself, so we get that here.
				 */
				$class_name = $token[1];
			}
		} while (($token = next($tokens)) !== false);
	}
}
