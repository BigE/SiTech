<?php
/**
 * SiTech/Loader.php
 *
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
 * @author Eric Gach <eric at php-oop.net>
 * @copyright SiTech Group (c) 2009
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Loader
 * @todo Fix documentation for whole SiTech_Loader class
 * @version $Id$
 */

/**
 * SiTech_Loader
 *
 * This loader class adds functionality to the autoload function already built
 * into PHP. It also provides helper functions to make any application easier to
 * manage and build.
 *
 * @package SiTech_Loader
 */
class SiTech_Loader
{
	public static function autoload($class)
	{
		self::loadClass($class);
	}

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

	public static function loadClass($class)
	{
		if (class_exists($class, false) || interface_exists($class, false)) {
			return;
		}

		$file = str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
		include_once($file);

		if (!class_exists($class, false) || interface_exists($class, false)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('The class "%s" failed to load', array($class));
		}
	}

	public static function loadController($name)
	{
		$name = strtolower($name);
		$class = ucfirst($name).'Controller';
		if (class_exists($class, false)) return;

		/* Silence the error (if any), its handled by an exception */
		@include_once(SITECH_APP_PATH.'/controllers/'.$name.'.php');

		if (!class_exists($class, false)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('The controller "%s" failed to load', array($class));
		}

		return(new $class);
	}

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
