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
 * Description of Controller
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2011
 * @filesource
 * @package SiTech\Controller
 * @version $Id$
 */
class Controller
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
		if (\is_array($path)) {
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
	static public function dispatch(\SiTech\Uri $uri)
	{
		$rewrite = $uri->isRewrite();

		if ($rewrite) {
			$path = $uri->getPath(\SiTech\Uri\FLAG_REWRITE);
		} else {
			$path = $uri->getPath();
		}

		foreach (self::$_routes as $regex => $array) {
			if (\preg_match("#^($regex)$#", $path, $parts)) {
				$uri->setController($array[0]);
				$uri->setAction($array[1]);
				if (!empty($parts[2]) && $parts[2][0] != '.') {
					$uri->setPath('/'.$array[0].'/'.$array[1].'/'.$parts[2], true);
				} elseif (!empty($parts[2]) && $parts[2][0] == '.') {
					$uri->setFormat(substr($parts[2], 1));
					$uri->setPath('/'.$array[0].'/'.$array[1].$parts[2], true);
				} else {
					$uri->setPath('/'.$array[0].'/'.$array[1], true);
				}
				$rewrite = true;
				break;
			}
		}

		$controller = $uri->getController();
		$action = $uri->getAction();
		$parts = \explode('/', $uri->getPath(true));
		$format = $uri->getFormat();
		if (isset($parts[0])) {
			$path = '/'.$parts[0];
		} else {
			$path = '';
		}

		if (\sizeof($parts) > 1) {
			$i = 1;
			while (\is_dir(\SITECH_APP_PATH.\DIRECTORY_SEPARATOR.'controllers'.\DIRECTORY_SEPARATOR.$path)) {
				$path .= \DIRECTORY_SEPARATOR.$parts[$i];
				if (!empty($action) && $parts[$i] == $action) unset($action);
				if (!$rewrite) $controller .= \DIRECTORY_SEPARATOR.$parts[$i];
				// This has to be incremented here no matter what
				$i++;
			}

			if (empty($parts[$i])) {
				if (empty($action)) $action = 'index';
			} else {
				if (!$rewrite) $action = $parts[$i];
				$path .= '/'.$parts[$i];
				for (++$i; $i < \sizeof($parts); $i++) {
					$path .= '/'.$parts[$i];
				}
			}
		}

		if (($fPos = \strrpos($action, '.')) !== false) {
			$format = \substr($action, $fPos+1);
			$action = \substr($action, 0, $fPos);
		}

		/**
		 * Changed to set just the path, that way we don't loose the whole URL
		 * when passing to a controller.
		 */
		$uri->setController($controller);
		$uri->setAction($action);
		$uri->setPath($path);
		$uri->setFormat($format);
		require_once('Loader.php');
		$obj = \SiTech\Loader::loadController($controller, $uri);
	}
}

namespace SiTech\Controller;

require_once('Exception.php');
class Exception extends \SiTech\Exception {}
