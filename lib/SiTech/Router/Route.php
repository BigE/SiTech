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

namespace SiTech\Router;

/**
 * @see \SiTech\Router
 */
require_once('SiTech/Router.php');

/**
 * These are the routes for the router. Each route takes a regex to match against
 * the incoming path. If you use named parameters in the regex for controller
 * or action, the default will be overwritten by the path. Additionally the
 * arguments to be passed into the controller can be passed through the regex
 * too. The match() method will take SITECH_PATH_PREFIX out of the path  before
 * matching if it is defined.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Router
 * @see preg_match
 * @version $Id$
 */
class Route
{
	const OPT_CONTROLLER = 1;
	const OPT_ACTION = 2;

	/**
	 * Action of router to call
	 *
	 * @var string
	 */
	protected $_action;

	/**
	 * Controller to use with the router
	 *
	 * @var string
	 */
	protected $_controller;

	/**
	 * Matches matched by the router.
	 *
	 * @var array
	 */
	protected $_match = array();

	/**
	 * The route that we're looking for
	 *
	 * @var string
	 */
	protected $_route;

	/**
	 * Build the route up. If no controller or action are specified, we set
	 * defaults.
	 *
	 * @param string $route
	 * @param string $controller
	 * @param string $action
	 */
	public function __construct($route, array $options = array())
	{
		$this->_route = $route;
		// If we have no deafult values, fill them in.
		$this->_controller = (isset($options[self::OPT_CONTROLLER]))? $options[self::OPT_CONTROLLER] : 'default';
		$this->_action = (isset($options[self::OPT_ACTION]))? $options[self::OPT_ACTION] : 'index';
	}

	/**
	 * This will dispatch the controller/action that is tied to this route. This
	 * can be called without matching the route, but is automatically called from
	 * the router when the route is matched. If the action does not return false
	 * this will try to call the display() method to render the view.
	 */
	public function dispatch()
	{
		// We have to have \SiTech\Loader here for this to work reliably... sorry.
		require_once('SiTech/Loader.php');
		\SiTech\Loader::loadController($this->_controller);

		$controller = new $this->_controller($this);
		if (!\method_exists($controller, $this->_action)) {
			throw new Exception('No method defined %s::%s', array($this->_controller, $this->_action));
		}

		if ($controller->{$this->_action}() !== false && $this->_controller instanceof \SiTech\Controller\AController) {
			$controller->display();
		}
	}

	/**
	 * Match the path specified against the route defined.
	 *
	 * @param string $path If you pass \SiTech\Uri here we will take the path
	 *                     from the object.
	 * @return \SiTech\Router\Route Returns false if it does not match.
	 */
	public function match($path)
	{
		// If the path is a Uri instance, we need to get the path out of it.
		if ($path instanceof \SiTech\Uri) {
			$path = $path->getPath();
		}

		if (empty($this->_route)) {
			// Default route, go!
			return($this);
		}

		// Trim the path prefix if defined. We do this here instead of the router
		// because this can be directly called.
		if (\defined('SITECH_PATH_PREFIX') && \substr($path, 0, \strlen(\SITECH_PATH_PREFIX)) === \SITECH_PATH_PREFIX) $path = \substr($path, \strlen(\SITECH_PATH_PREFIX));
		// Match the path against the route
		if (\preg_match('#'.\str_replace('#', '\#', $this->_route).'#', $path, $m)) {
			if (!empty($m['controller'])) $this->_controller = $m['controller'];
			// Remove the controller from the path
			unset($m['controller']);
			if (!empty($m['action'])) $this->_action = $m['action'];
			// Remove the action from the path
			unset($m['action']);
			$this->_match = $m;

			return($this);
		}

		// No match... sad panda
		return(false);
	}
}
