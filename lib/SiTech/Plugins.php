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

namespace SiTech;

/**
 * This plugin class will take PHP code, load it into the tokenizer, then parse
 * it out into "plugins" that can then be destroyed or reloaded.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2010-2011
 * @package SiTech
 * @version $Id$
 */
class Plugins
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
			throw new Plugins\Exception('The plugin file "%s" could not be loaded because it exceeds the maximum filesize of %d KB', array($file, $this->_attributes[self::ATTR_MAX_FILESIZE]));
		}

		if (!file_exists($file) || !is_readable($file) || ($contents = php_strip_whitespace($file)) === false) {
			require_once('SiTech/Plugins/Exception.php');
			throw new Plugins\Exception('The plugin file "%s" could not be read', array($file));
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

namespace SiTech\Plugins;
require_once('Exception.php');

/**
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Plugins
 * @version $Id$
 */
class Exception extends \SiTech\Exception {}
