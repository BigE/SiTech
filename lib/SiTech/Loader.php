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

namespace SiTech;

/**
 * SiTech_Loader
 *
 * This loader class adds functionality to the autoload function already built
 * into PHP. It also provides helper functions to make any application easier to
 * manage and build.
 *
 * @author Eric Gach <eric at php-oop.net>
 * @copyright SiTech Group (c) 2009-2011
 * @filesource
 * @package SiTech\Loader
 * @todo Fix documentation for whole SiTech\Loader class
 * @version $Id$
 */
class Loader
{
	/**
	 * Auto load the class by calling the loadClass method.
	 *
	 * @param string $class
	 * @see loadClass
	 */
	public static function autoload($class)
	{
		self::loadClass($class);
	}

	/**
	 * Load the bootstrap file. By default it will look inside of
	 * SITECH_APP_PATH/applications/ but the file can be specified by passing
	 * a string to the path.
	 *
	 * @param string $file
	 * @throws SiTech\Loader\Exception
	 */
	public static function loadBootstrap($file = null)
	{
		if (empty($file)) {
			if (!\defined('SITECH_APP_PATH')) {
				throw new Loader\Exception('SITECH_APP_PATH not defined. Unable to detect path to find bootstrap file');
			} else {
				$file = \SITECH_APP_PATH.'/bootstrap.php';
			}
		}

		if (!\is_readable($file)) {
			throw new Loader\Exception('Unable to load bootsrap file "%s"', array($file));
		}

		require($file);
	}

	/**
	 * Load a class up for use with the current application. If the class is
	 * already defined, it will simply return without doing anything. This
	 * method will also load interfaces.
	 *
	 * @param string $class
	 * @throws SiTech\Loader\Exception
	 */
	public static function loadClass($class)
	{
		if (\class_exists($class, false) || \interface_exists($class, false)) {
			return;
		}

		$file = \str_replace('_', \DIRECTORY_SEPARATOR, $class).'.php';
		include_once($file);

		if (!\class_exists($class, false) && !\interface_exists($class, false)) {
			throw new Loader\Exception('The class "%s" failed to load', array($class));
		}
	}

	/**
	 * Load a controller into the current application. By default this will try
	 * to load from SITECH_APP_PATH/controllers/. If the controller fails to
	 * load an exception will be thrown.
	 *
	 * @param string $name
	 * @param SiTech_Uri $uri
	 * @return object
	 * @throws SiTech\Loader\Exception
	 */
	public static function loadController($name, SiTech_Uri $uri)
	{
		$name = \strtolower($name);
		$class = null;

		if (\strstr($name, '/') !== false) {
			$parts = \explode('/', $name);
			$parts = \array_map('ucfirst', $parts);
			$class = \implode('_', $parts).'Controller';
		} else {
			$class = \ucfirst($name).'Controller';
		}

		if (\class_exists($class, false)) return;

		if (\is_readable(\SITECH_APP_PATH.'/controllers/'.$name.'.php')) {
			include_once(\SITECH_APP_PATH.'/controllers/'.$name.'.php');
		} else {
			throw new Loader\Exception('The controller "%s" failed to load', array($class), 404);
		}

		if (!\class_exists($class, false)) {
			throw new Loader\Exception('The controller "%s" failed to load', array($class), 500);
		}

		return(new $class($uri));
	}

	/**
	 * Load a model class for the application to use. This will look at
	 * SITECH_APP_PATH/models/ for the model. If the model fails to load an
	 * exception will be thrown.
	 *
	 * @param string $model
	 * @throws SiTech\Loader\Exception
	 */
	public static function loadModel($model)
	{
		$name = \strtolower($model);
		$class = null;

		if (\strstr($name, '/') !== false) {
			$parts = \explode('/', $name);
			$parts = \array_map('ucfirst', $parts);
			$class = \implode('_', $parts).'Model';
		} else {
			$class = \ucfirst($name).'Model';
		}

		if (\class_exists($class, false)) return;

		if (\is_readable(\SITECH_APP_PATH.'/models/'.$name.'.php')) {
			include_once(\SITECH_APP_PATH.'/models/'.$name.'.php');
		} else {
			throw new Loader\Exception('The model "%s" failed to load', array($class));
		}

		if (!\class_exists($class, false)) {
			throw new Loader\Exception('The model "%s" failed to load', array($class));
		}
	}

	/**
	 * Register a different class for autoloading code into the application. Doing
	 * this will override any methods in this class. If something fails, an
	 * exception will be thrown.
	 *
	 * @param string $class
	 * @param bool $enabled
	 * @throws SiTech\Loader\Exception
	 */
	public static function registerAutoload($class = 'SiTech_Loader', $enabled = true)
	{
		if (!\function_exists('spl_autoload_register')) {
			throw new Loader\Exception('spl_autoload does not exist in this PHP installation');
		}

		self::loadClass($class);
		$methods = \get_class_methods($class);
		if (!\in_array('autoload', (array)$methods)) {
			throw new Loader\Exception('The class "%s" does not have an autoload() method', array($class));
		}

		if ($enabled === true) {
			\spl_autoload_register(array($class, 'autoload'));
		} else {
			\spl_autoload_unregister(array($class, 'autoload'));
		}
	}
}

namespace SiTech\Loader;

/**
 * @see SiTech\Exception
 */
require_once('Exception.php');

/**
 * Exception class for the loader part of SiTech.
 *
 * @author Eric Gach <eric at php-oop.net>
 * @copyright SiTech Group (c) 2011
 * @filesource
 * @package SiTech\Loader
 * @version $Id$
 */
class Exception extends \SiTech\Exception {}
