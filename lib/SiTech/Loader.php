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
 * This loader class adds functionality to the autoload function already built
 * into PHP. It also provides helper functions to make any application easier to
 * manage and build.
 *
 * @author Eric Gach <eric at php-oop.net>
 * @package SiTech
 * @version $Id$
 */
class Loader
{
	/**
	 * Add a vendor directory to the include path. You can specify the entire
	 * path by the parameters avaialble.
	 *
	 * @param string $vendor Vendor name to add to the include path.
	 * @param string $lib_path Library path inside the vendor to use. Default: lib
	 * @param string $path_prefix Path prefix to the vendor folder. Default: SITECH_APP_PATH/../vendors/
	 * @return string
	 * @static
	 */
	public static function addVendor($vendor, $lib_path = 'lib', $path_prefix = null, $strict_as_hell = false)
	{
		if (empty($path_prefix))
			$path_prefix = \dirname(\SITECH_APP_PATH).\DIRECTORY_SEPARATOR.'vendors';

		$vendor_path = $path_prefix.\DIRECTORY_SEPARATOR.$vendor.\DIRECTORY_SEPARATOR.ltrim($lib_path, '/\\');

		// Bahaha, thanks Tim ;)
		if ($strict_as_hell && \strpos(\get_include_path(), $vendor_path)) {
			throw new Loader\VendorAlreadyPresent('The vendor include path (%s) is already in the include_path', array($vendor_path));
		}

		\set_include_path($vendor_path.\PATH_SEPARATOR.\get_include_path());
		return($vendor_path);
	}

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
		if (\substr($class, -5) == 'Model') {
			self::loadModel(\substr($class, 0, -5));
		} elseif (\substr($class, -10) == 'Controller') {
			self::loadController(\substr($class, 0, -10));
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
	 * @static
	 * @throws SiTech\Loader\Exception
	 */
	public static function loadClass($class)
	{
		if (\class_exists($class, false) || \interface_exists($class, false)) {
			return;
		}

		$file = \str_replace(array('_', '\\'), \DIRECTORY_SEPARATOR, $class).'.php';
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
	 * @return object
	 * @static
	 * @throws SiTech\Loader\Exception
	 */
	public static function loadController($name)
	{
		$name = \strtolower($name);
		$class = null;
		$nclass = null;

		if (\strstr($name, '/') !== false) {
			$parts = \explode('/', $name);
			$parts = \array_map('ucfirst', $parts);
			$nclass = '\\'.\implode('\\', $parts).'Controller';
			$class = \implode('_', $parts).'Controller';
		} else {
			$class = \ucfirst($name).'Controller';
		}

		if (\class_exists($class, false) || \class_exists($nclass, false)) return;

		if (\is_readable(\SITECH_APP_PATH.'/controllers/'.$name.'.php')) {
			include_once(\SITECH_APP_PATH.'/controllers/'.$name.'.php');
		} else {
			throw new Loader\Exception('The controller "%s" failed to load', array($class), 404);
		}

		if (!\class_exists($class, false) && !\class_exists($nclass, false)) {
			throw new Loader\Exception('The controller "%s" failed to load', array($class), 500);
		}

		return((class_exists($nclass, false))? new $nclass() : new $class());
	}

	/**
	 * Load a model class for the application to use. This will look at
	 * SITECH_APP_PATH/models/ for the model. If the model fails to load an
	 * exception will be thrown.
	 *
	 * @param string $model Name of the model to load
	 * @static
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
	 * @param string $class Name of the class to be used for an autoloader
	 * @param bool $enabled If set to true, the autoloader will be registered, if
	 *                      it is false, it will be unregistered.
	 * @static
	 * @throws SiTech\Loader\Exception
	 */
	public static function registerAutoload($class = 'SiTech\Loader', $enabled = true)
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
 * @package SiTech\Loader
 * @version $Id$
 */
class Exception extends \SiTech\Exception {}

/**
 * Exception for adding vendors to the include path. If strict_as_hell is true
 * this exception will be thrown if the vendor already exists.
 *
 * @author Eric Gach <eric at php-oop.net>
 * @package SiTech\Loader
 * @version $Id$
 */
class VendorAlreadyPresent extends Exception {}
