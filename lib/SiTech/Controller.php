<?php

/**
 * Description of Controller
 *
 * @author Eric Gach <eric@php-oop.net>
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
