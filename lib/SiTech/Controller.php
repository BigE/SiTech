<?php
/**
 * SiTech/Controller.php
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
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2009
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Controller
 * @version $Id$
 */

/**
 * Description of Controller
 *
 * @package SiTech_Controller
 */
class SiTech_Controller
{
	protected static $_routes = array();

	/**
	 * Add a route to the controller for easier parsing.
	 *
	 * @param <type> $path A preg regex to match the path.
	 * @param <type> $controller
	 * @param <type> $action
	 */
	static public function addRoute($path, $controller, $action)
	{
		if (is_array($path)) {
			foreach ($path as $item) {
				self::$_routes[$item] = array($controller, $action);
			}
		} else {
			self::$_routes[$path] = array($controller, $action);
		}
	}

	/**
	 * Dispatch the controller. This will load the controller using the
	 * SiTech_Loader class. It also sets the correct path for the URL if
	 * the URL is rewritten.
	 *
	 * @param SiTech_Uri $uri
	 */
	static public function dispatch(SiTech_Uri $uri)
	{
		foreach (self::$_routes as $regex => $array) {
			if (preg_match("#^($regex)$#", $uri->getPath(), $parts)) {
				$uri->setController($array[0]);
				$uri->setAction($array[1]);
				break;
			}
		}


		$controller = $uri->getController();
		$action = $uri->getAction();
		$parts = explode('/', $uri->getPath(true));

		if (sizeof($parts) > 1) {
			$i = 1;
			while (is_dir(SITECH_APP_PATH.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller)) {
				$controller .= '/'.$parts[$i++];
			}

			if (empty($parts[$i])) {
				$action = 'index';
				$path = '/'.$controller;
			} else {
				$action = $parts[$i];
				$path = '/'.$controller.'/'.$action;
				for (++$i; $i < sizeof($parts); $i++) {
					$path .= '/'.$parts[$i];
				}
			}
		}

		/**
		 * Changed to set just the path, that way we don't loose the whole URL
		 * when passing to a controller.
		 */
		$uri->setController($controller);
		$uri->setAction($action);
		if (!empty($path)) {
			$uri->setPath($path);
		}
		$obj = SiTech_Loader::loadController($controller, $uri);
	}
}
