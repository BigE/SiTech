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

namespace SiTech\Routing;

/**
 * @see \SiTech\Routing\Exception
 */
require_once('SiTech/Routing/Exception.php');

/**
 * These are the routes for the router. Each route takes a regex to match against
 * the incoming path. If you use named parameters in the regex for controller
 * or action, the default will be overwritten by the path. Additionally the
 * arguments to be passed into the controller can be passed through the regex
 * too. The match() method will take SITECH_PATH_PREFIX out of the path  before
 * matching if it is defined.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Routing
 * @see preg_match
 * @version $Id: e20fd8a8d00a8dc80534fef6de77a996fa78f1f9 $
 */
class Route
{
	/**
	 * This is the controller option that can be used in the route.
	 */
	const OPT_CONTROLLER = 0;

	/**
	 * This is the action which is the method to call in the controller.
	 */
	const OPT_ACTION = 1;

	/**
	 * This is the namespace in which the routes controller.
	 */
	const OPT_NAMESPACE = 2;

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
	 * This is the namespace for the route. Namespaced routes are for controllers
	 * that are nested into sub directories for easier organization of the
	 * controller files.
	 *
	 * @var string
	 */
	protected $_namespace;

	/**
	 * The route that we're looking for
	 *
	 * @var string
	 */
	protected $_route;

	/**
	 * Build the route up. If no controller or action are specified, we set
	 * defaults. The default controller will be 'Default' but can be overridden
	 * using the constant SITECH_CONTROLLER_DEFAULT.
	 *
	 * @param string $route Regex string of route to match
	 * @param array $options Options to use for the route
	 */
	public function __construct($route, array $options = array())
	{
		$this->_route = $route;
		// If we have no deafult values, fill them in.
		$this->_controller = (isset($options[self::OPT_CONTROLLER]))? $options[self::OPT_CONTROLLER] : ((\defined('SITECH_CONTROLLER_DEFAULT'))? \SITECH_CONTROLLER_DEFAULT : 'default');
		$this->_action = (isset($options[self::OPT_ACTION]))? $options[self::OPT_ACTION] : 'index';
		
		if (isset($options[self::OPT_NAMESPACE])) {
			$this->_namespace = $options[self::OPT_NAMESPACE];
		}
	}

	public function __get($name)
	{
		if (isset($this->_match[$name])) {
			return($this->_match[$name]);
		} else {
			return(null);
		}
	}

	public function __isset($name)
	{
		return((isset($this->_match[$name]))? true : false);
	}

	/**
	 * This will dispatch the controller/action that is tied to this route. This
	 * can be called without matching the route, but is automatically called from
	 * the router when the route is matched. If the action does not return false
	 * this will try to call the display() method to render the view.
	 *
	 * @throws SiTech\Routing\MethodNotFoundException
	 */
	public function dispatch()
	{
		require_once('SiTech/Loader.php');
		$controller = \SiTech\Loader::loadController($this->_controller);

		if (!\method_exists($controller, $this->_action)) {
			throw new MethodNotFoundException('No method defined %s::%s', array(get_class($controller), $this->_action));
		}

		if ($controller->{$this->_action}() !== false && $controller instanceof \SiTech\Controller\Base) {
			$controller->display($this->_controller.DIRECTORY_SEPARATOR.$this->_action.'.tpl');
		}
	}

	/**
	 * Get the action of the controller called by the route.
	 *
	 * @return string
	 */
	public function getAction()
	{
		return($this->_action);
	}

	/**
	 * Get the controller name that is called by the route.
	 *
	 * @return string
	 */
	public function getController()
	{
		return($this->_controller);
	}

	public function getNamespace()
	{
		return(ltrim($this->_namespace, '/'));
	}

	/**
	 * Match the path specified against the route defined.
	 *
	 * @param string $path If you pass \SiTech\Uri here we will take the path
	 *                     from the object.
	 * @return SiTech\Routing\Route Returns false if it does not match.
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
		// If we have a namespace, pull it off the URI before matching. It will
		// be placed into the controller name later.
		if (isset($this->_namespace)) {
			if (\strpos($path, '/'.$this->_namespace) === 0) {
				$path = substr($path, \strlen($this->_namespace)+1);
			} else {
				return(false);
			}
		}
		// Match the path against the route
		if (\preg_match('#'.\str_replace('#', '\#', $this->_route).'#', $path, $m)) {
			$this->_match = $m;
			if (!empty($m['controller'])) $this->_controller = $m['controller'];
			if (!empty($this->_namespace)) $this->_controller = $this->_namespace.'/'.$this->_controller;
			// Remove the controller from the path
			unset($m['controller']);
			if (!empty($m['action'])) $this->_action = $m['action'];
			// Remove the action from the path
			unset($m['action']);

			// Get any unmapped arguments passed in the URL
			$this->_args = explode('/', substr($path, strlen($m[0])+1));

			return($this);
		}

		// No match... sad panda
		return(false);
	}
}
