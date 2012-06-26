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

/**
 * This loader class adds functionality to the autoload function already built
 * into PHP. It also provides helper functions to make any application easier to
 * manage and build.
 *
 * @author Eric Gach <eric at php-oop.net>
 * @package SiTech
 * @version $Id$
 */
class SiTech_Loader
{
	/**
	 * Auto load the class by calling the loadClass method. This method detects
	 * the last part of a class name and loads appropriately. If the class ends
	 * in "Model" it calls ::loadModel() If it ends in "Controller" it calls
	 * ::loadController() otherwise ::loadClass() is called.
	 *
	 * @param string $class This is the class name that is to be autoloaded
	 * @static
	 */
	public static function autoload($class)
	{
		if (substr($class, -5) == 'Model' && substr($class, -6, 1) !== '_') {
			self::loadModel(substr($class, 0, -5));
		} elseif (substr($class, -10) == 'Controller' && substr($class, -11, 1) !== '_') {
			self::loadController(str_replace('_', DIRECTORY_SEPARATOR, substr($class, 0, -10)), null);
		} else {
			self::loadClass($class);
		}
	}

	/**
	 * Load the bootstrap file. By default it will look inside of
	 * SITECH_APP_PATH/applications/ but the file can be specified by passing
	 * a string to the path.
	 *
	 * @param string $file
	 * @static
	 * @throws SiTech_Exception
	 */
	public static function loadBootstrap($file = null)
	{
		if (empty($file)) {
			if (!defined('SITECH_APP_PATH')) {
				require_once('SiTech/Exception.php');
				throw new SiTech_Exception('SITECH_APP_PATH not defined. Unable to detect path to find bootstrap file');
			} else {
				$file = SITECH_APP_PATH.'/bootstrap.php';
			}
		}

		if (!is_readable($file)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('Unable to load bootsrap file "%s"', array($file));
		}

		require($file);
	}

	/**
	 * Load a class up for use with the current application. If the class is
	 * already defined, it will simply return without doing anything. This
	 * method will also load interfaces.
	 *
	 * @param string $class
	 * @static
	 * @throws SiTech\Loader\Exception
	 */
	public static function loadClass($class)
	{
		if (class_exists($class, false) || interface_exists($class, false)) {
			return;
		}

		$file = str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
		include_once($file);

		if (!class_exists($class, false) && !interface_exists($class, false)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('The class "%s" failed to load', array($class));
		}
	}

	/**
	 * Load a controller into the current application. By default this will try
	 * to load from SITECH_APP_PATH/controllers/. If the controller fails to
	 * load an exception will be thrown.
	 *
	 * @param string $name
	 * @return object
	 * @static
	 * @throws SiTech_Exception
	 */
	public static function loadController($name, SiTech_Uri $uri = null)
	{
		$name = strtolower($name);
		$class = null;

		if (strstr($name, DIRECTORY_SEPARATOR) !== false) {
			$parts = explode(DIRECTORY_SEPARATOR, $name);
			$parts = array_map('ucfirst', $parts);
			$class = implode('_', $parts).'Controller';
		} else {
			$class = ucfirst($name).'Controller';
		}

		if (class_exists($class, false)) return;

		if (is_readable(SITECH_APP_PATH.'/controllers/'.$name.'.php')) {
			include_once(SITECH_APP_PATH.'/controllers/'.$name.'.php');
		} else {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('The controller "%s" failed to load', array($class), 404);
		}

		if (!class_exists($class, false)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('The controller "%s" failed to load', array($class), 500);
		}
		
		if( $uri !== null )
			return(new $class($uri));
	}

	/**
	 * Load a model class for the application to use. This will look at
	 * SITECH_APP_PATH/models/ for the model. If the model fails to load an
	 * exception will be thrown.
	 *
	 * @param string $model Name of the model to load
	 * @static
	 * @throws SiTech_Exception
	 */
	public static function loadModel($model)
	{
		$name = strtolower($model);
		$class = null;

		if (strstr($name, '/') !== false) {
			$parts = explode('/', $name);
			$parts = array_map('ucfirst', $parts);
			$class = implode('_', $parts).'Model';
		} else {
			$class = ucfirst($name).'Model';
		}

		if (class_exists($class, false)) return;

		if (is_readable(SITECH_APP_PATH.'/models/'.$name.'.php')) {
			include_once(SITECH_APP_PATH.'/models/'.$name.'.php');
		} else {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('The model "%s" failed to load', array($class));
		}

		if (!class_exists($class, false)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('The model "%s" failed to load', array($class));
		}
	}

	/**
	 * Register a different class for autoloading code into the application. Doing
	 * this will override any methods in this class. If something fails, an
	 * exception will be thrown.
	 *
	 * @param string $class Name of the class to be used for an autoloader
	 * @param bool $enabled If set to true, the autoloader will be registered, if
	 *                      it is false, it will be unregistered.
	 * @static
	 * @throws SiTech_Exception
	 */
	public static function registerAutoload($class = 'SiTech_Loader', $enabled = true)
	{
        if (!function_exists('spl_autoload_register')) {
            require_once 'SiTech/Exception.php';
            throw new SiTech_Exception('spl_autoload does not exist in this PHP installation');
		}

		self::loadClass($class);
        $methods = get_class_methods($class);
        if (!in_array('autoload', (array)$methods)) {
            require_once 'SiTech/Exception.php';
            throw new SiTech_Exception('The class "%s" does not have an autoload() method', array($class));
		}

		if ($enabled === true) {
            spl_autoload_register(array($class, 'autoload'));
		} else {
            spl_autoload_unregister(array($class, 'autoload'));
		}
	}
}
