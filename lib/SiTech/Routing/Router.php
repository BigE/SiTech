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

namespace SiTech\Routing;

/**
 * @see \SiTech\Routing\Exception
 */
require_once('SiTech/Routing/Exception.php');

/**
 * This is the router for adding and matching routes based uppon a path that is
 * either specified or "guessed". If SITECH_PATH_PREFIX is defined, then that
 * portion of the path will be ignored by the route itself.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Routing
 * @see preg_match
 * @version $Id$
 */
class Router
{
	/**
	 * When dispatch is called, this variable is set to the route that was
	 * matched and dispatched. To retreive this route, you can call the
	 * getDispatchedRoute method.
	 *
	 * @see getDispatchedRoute
	 * @var \SiTech\Routing\Route
	 */
	protected static $_dispatchedRoute = false;

	/**
	 * Routes that are available to the router.
	 *
	 * @var array
	 */
	protected static $_routes = array();

	/**
	 * Add a route to the router. The router will match based on the order that
	 * the routes are added.
	 *
	 * @param \SiTech\Routing\Route $route
	 */
	public static function add(Route $route)
	{
		static::$_routes[] = $route;
	}

	/**
	 * Dispatch a route. If no route is specified, we look for the route based
	 * on the current path of the application. The route that gets dispached will
	 * be set internall and can be retreived using getDispatchedRoute
	 *
	 * @param \SiTech\Routing\Route $route
	 * @see getDispatchedRoute
	 * @throws \SiTech\Routing\NoRouteException
	 */
	public static function dispatch(\SiTech\Routing\Route $route = null)
	{
		if (empty($route)) $route = static::getRoute();
		if (!$route) throw new NoRouteException('No route was matched');
		self::$_dispatchedRoute = $route;

		$route->dispatch();
	}

	/**
	 * Return the route that was dispached through the router.
	 *
	 * @return \SiTech\Routing\Route Returns false if no route has been dispatched.
	 * @see dispatch
	 */
	public static function getDispatchedRoute()
	{
		return(self::$_dispatchedRoute);
	}

	/**
	 * Get the current route based on the path. If no path is specified, we will
	 * use the REQUEST_URI.
	 *
	 * @param string $path If \SiTech\Uri is passed in, the path will be retreived
	 *                     from that.
	 * @return \SiTech\Routing\Route
	 */
	public static function getRoute($path = null)
	{
		if (empty($path)) $path = $_SERVER['REQUEST_URI'];
		if ($path instanceof \SiTech\Uri) $path = $path->getPath();

		return(static::matchRoute($path));
	}

	/**
	 * Match the route based on the specified path. If no path is specified, we
	 * will use the REQUEST_URI.
	 *
	 * @param string $path If \SiTech\Uri is passed in, the path will be retreived
	 *                     from the object.
	 * @return \SiTech\Routing\Route Returns false if no route is matched.
	 */
	public static function matchRoute($path = null)
	{
		if (empty($path)) $path = $_SERVER['REQUEST_URI'];
		if ($path instanceof \SiTech\Uri) $path = $path->getPath();

		foreach (static::$_routes as $route) {
			if (($match = $route->match($path)) !== false) break;
		}

		return($match);
	}
}
