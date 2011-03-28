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
 * @package SiTech\Controller
 */

namespace SiTech\Controller;

/**
 * Description of Abstract
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group © 2008-2011
 * @filesource
 * @package SiTech\Controller
 * @version $Id$
 */
abstract class AController
{
	/**
	 * Internal use for the action of the controller.
	 *
	 * @var string Name of action for this controller.
	 */
	protected $_action;

	/**
	 * Internal argument map so the controller knows what arguments to map to
	 * what variables.
	 *
	 * @var array
	 */
	protected $_argMap = array();

	/**
	 * Arguments received by the controller. If they don't have a named key
	 * in the $_argMap array, they will only be available through the numerical
	 * index.
	 *
	 * @var array
	 */
	protected $_args;

	/**
	 * Names of components to load when the controller is initalized. These
	 * should exist inside of the controllers/components folder.
	 *
	 * @var array
	 */
	protected $_components = array();

	/**
	 * This tells the display() method if it has been called yet.
	 *
	 * @see display
	 * @var boolean
	 */
	private $_display = false;

	/**
	 * Any errors encountered during processing by the controller should be put
	 * into this array.
	 *
	 * @var array
	 */
	protected $_errors = array();

	/**
	 * If $_GET['xhr'] or $_POST['xhr'] is set, the request is a XHR request
	 * and this will be set to true.
	 *
	 * @var boolean
	 */
	protected $_isXHR = false;

	/**
	 * The layout to use for the current page. If it is empty, the layout will
	 * remain unused.
	 *
	 * @var string
	 */
	protected $_layout;

	/**
	 * Models to load during the constructor. These are loaded before the
	 * components are loaded.
	 *
	 * @var array
	 */
	protected $_models = array();

	/**
	 * If passed into the constructor, this is the route that was matched by the
	 * router to trigger this controller.
	 *
	 * @var \SiTech\Router\Route
	 */
	protected $_route;

	/**
	 * This is the view used by the controller.
	 *
	 * @see display setLayout
	 * @var \SiTech\Template
	 */
	protected $_view;

	/**
	 * This is the guts of the controller. Once here, we do all the processing
	 * and setup needed.
	 */
	public function __construct(\SiTech\Router\Route $route = null)
	{
		$this->_route = $route;

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && \strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->_isXHR = true;
		}

		// Load models first, in case the components need them.
		if (!empty($this->_models)) {
			require_once('SiTech/Loader.php');
			\array_walk($this->_models, array('\SiTech\Loader', 'loadModel'));
		}

		// Load components
		if (!empty($this->_components)) {
			foreach ($this->_components as $component) {
				if (\class_exists($component, false)) continue;

				require_once(\SITECH_APP_PATH.'/controllers/components/'.$component.'.php');
				if (!\class_exists($component, false)) {
					require_once('SiTech/Controller.php');
					throw new Exception('Failed to load component %s', array($component));
				}

				$this->$component = new $component($this);
			}
		}

		/**
		 * If the parent doesn't define its own view, set a generic view.
		 */
		if (empty($this->_view)) {
			require_once('SiTech/Template.php');
			$this->_view = new \SiTech\Template(\SITECH_APP_PATH.\DIRECTORY_SEPARATOR.'views');
		}

		// let our template know if we're using a XML HTTP Request.
		$this->_assign('_isXHR', $this->_isXHR);
	}

	/**
	 * This is a built in helper method for displaying the template once it is
	 * built by the controller. If the controller does not call this method, the
	 * dispatch of the route will automatically call it. If you do not want it
	 * automatically called by the route, return false from the method and it
	 * will not call this method.
	 *
	 * @param string $page Template page to display.
	 * @param string $type Content Type to send for template output.
	 */
	public function display($page, $type = 'text/html')
	{
		if ($this->_display === true) return;

		if (!empty($this->_layout)) {
			$this->setLayout($this->_layout);
		}

		$this->_display = true;
		$this->_view->display($page, $type);
	}

	/**
	 * This is a helper method for the view to assign variables to the view.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	protected function _assign($name, $value)
	{
		$this->_view->assign($name, $value);
	}

	/**
	 * Create an array for paging results. This will return an array of all pages
	 * with a url to each and a "prev" and "next" value set to true/false if
	 * there is a prev/next page. Pass [page] into the link where the page
	 * number should be in the URL.
	 *
	 * @param int $totalRecords
	 * @param int $limit
	 * @param string $link
	 * @param int $current
	 * @return array
	 */
	protected function _paging($totalRecords, $limit, $link, $current = 1)
	{
		$current = (int)$current;
		$pages = array();

		if ($current < 1) $current = 1;

		$i = 1;
		do {
			$pages[] = array(
				'current' => ($current === $i),
				'link'    => \str_replace('[page]', $i, $link),
				'next'    => ((($i * $limit) >= $totalRecords)? false : $i + 1),
				'number'  => $i,
				'prev'    => (($i > 1)? true : false)
			);

		} while (($i++ * $limit) <= $totalRecords);

		return($pages);
	}

	/**
	 * Helper function to set the layout with the view. This is automatically
	 * called by the display method if the layout is defined in the instance
	 * variable $_layout
	 *
	 * @param string $layout
	 */
	protected function _setLayout($layout)
	{
		$this->_view->setLayout($layout);
	}
}
