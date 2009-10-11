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

	static public function dispatch(SiTech_Uri $uri)
	{
		if (empty(self::$_routes[$uri->getPath()])) {
			$parts = explode('/', $uri->getPath(true), 3);
			$controller = (empty($parts[0]))? 'default' : $parts[0];
			$action = (empty($parts[1]))? 'index' : $parts[1];
		} else {
			$controller = self::$_routes[$uri->getPath()][0];
			$action = self::$_routes[$uri->getPath()][1];
		}

		$uri = '/'.$controller.'/'.$action;
		if (!empty($parts[2])) {
			$uri .= '/'.$parts[2];
		}

		$uri = new SiTech_Uri($uri);
		$obj = SiTech_Loader::loadController($controller, $uri);
	}
}
