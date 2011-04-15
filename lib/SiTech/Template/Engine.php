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

namespace SiTech\Template;

require_once('SiTech/Template/Exception.php');

/**
 * This is the template class for all templates. Here you can
 * assign variables, render the page, and even display the full output.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Template
 * @version $Id$
 */
class Engine
{
	/**
	 * Toggles if the template should be in strict mode or not. When a template
	 * is in strict mode, variables cannot be overwritten, and error reporting
	 * will include notices during rendering. A boolean value should be assigned
	 * to this attribute.
	 */
	const ATTR_STRICT = 0;

	/**
	 * Rendering engine to use. Should be a full class name to use. If none is
	 * set, we default to SiTech_Template_Renderer_PHP.
	 */
	const ATTR_RENDER_ENGINE = 1;

	/**
	 * Attributes set within the current template.
	 *
	 * @var array
	 */
	protected $_attributes = array();

	/**
	 * Layout to use for the template file we display. If no layout is set the
	 * template itself will be displayed.
	 *
	 * @var string
	 */
	protected $_layout;

	/**
	 * Template path to locate files at.
	 *
	 * @var string
	 */
	protected $_path;

	/**
	 * Variables set for the current template
	 *
	 * @var array
	 */
	protected $_vars = array();

	/**
	 * This will initalize the engine by setting the path of where to find files
	 * and any additional options.
	 *
	 * @param string $path Path where to load the template file(s) from.
	 * @param array $options Options to set for the template engine.
	 * @return void
	 */
	public function __construct($path = null, array $options = array())
	{
		if (!empty($path)) {
			$this->_path = \realpath($path);
		} elseif (defined('SITECH_APP_PATH') && is_dir(SITECH_APP_PATH.'/views')) {
			$this->_path = SITECH_APP_PATH.'/views';
		}

		if (!empty($options)) {
			foreach ($options as $attr => $value) {
				$this->setAttribute($attr, $value);
			}
		}
	}

	/**
	 * Magic get method. This will pull an assigned value from the class.
	 *
	 * @param string $name Name of the variable to get
	 * @return mixed
	 * @see assign
	 */
	public function __get($name)
	{
		if (isset($this->_vars[$name])) {
			return($this->_vars[$name]);
		} else {
			return(null);
		}
	}

	/**
	 * Magic set method. This is basically another way of using the assign
	 * method to set variables for the template.
	 *
	 * @param string $name Name of variable to set in template
	 * @param mixed $value Value to set variable to
	 * @return void
	 * @see assign unassign
	 */
	public function __set($name, $value)
	{
		$this->assign($name, $value);
	}

	/**
	 * Assign a variable to the current template file. If strict mode is enabled
	 * and a variable is already set, an exception will be thrown to notify the
	 * user.
	 *
	 * @param string $name Name of variable to be assigned.
	 * @param mixed $val Value of variable to be used in template.
	 * @return bool Returns TRUE on success FALSE on failure.
	 * @throws SiTech\Template\Exception
	 */
	public function assign($name, $val)
	{
		if ((bool)$this->getAttribute(self::ATTR_STRICT) && isset($this->_vars[$name])) {
			throw new AlreadySetException('Cannot overwrite previously set template variable %s due to strict restrictions', array($name));
		}

		$this->_vars[$name] = $val;
		return(true);
	}

	/**
	 * Output the current template file. This just echos the output of the
	 * render method.
	 *
	 * @param string $page Page name to render and display.
	 * @param string $type Document type header to send before displaying.
	 * @return void The page is displayed, nothing is returned.
	 * @see render
	 */
	public function display($page, $type = 'text/html')
	{
		\header('Content-Type: '.$type);
		$content = $this->render($page);
		\header('Content-Length: '.\strlen($content));
		echo $content;
		unset($content);
	}

	/**
	 * Return the proper doctype tag for use in HTML documents. Default is
	 * XHTML 1.0 Strict.
	 *
	 * @param string $doctype
	 * @return string
	 */
	public function doctype($doctype = 'XHTML_10_STRICT')
	{
		switch ($doctype) {
			case 'HTML_401_STRICT':
				$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">';
				break;

			case 'HTML_401_TRANS':
				$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">';
				break;

			case 'HTML_401_FRAME':
				$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">';
				break;

			case 'XHTML_10_STRICT':
				$doctype = '<?xml version="1.0" encoding="utf-8"?>'.\PHP_EOL.'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
				break;

			case 'XHTML_10_TRANS':
				$doctype = '<?xml version="1.0" encoding="utf-8"?>'.\PHP_EOL.'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
				break;

			case 'XHTML_10_FRAME':
				$doctype = '<?xml version="1.0" encoding="utf-8"?>'.\PHP_EOL.'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
				break;

			case 'XHTML_11':
				$doctype = '<?xml version="1.0" encoding="utf-8"?>'.\PHP_EOL.'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
				break;
		}

		return($doctype);
	}

	/**
	 * Get the value of the specified attribute.
	 *
	 * @param int $attr SiTech_Template::ATTR_* constant.
	 * @return mixed If the attribute is not set, null will be returned.
	 */
	public function getAttribute($attr)
	{
		if (isset($this->_attributes[$attr])) {
			return($this->_attributes[$attr]);
		} else {
			return(null);
		}
	}

	/**
	 * Get the current layout set for the template. If none is set, null will
	 * be returned.
	 *
	 * @return string
	 */
	public function getLayout()
	{
		$path = $this->_layout;
		if (\defined('SITECH_APP_PATH') && \is_dir(\SITECH_APP_PATH.'/layouts/')) $path = \SITECH_APP_PATH.'/layouts/'.$path;
		return($path);
	}

	/**
	 * This calls the rendering engine to parse the template file and return
	 * the complete output.
	 *
	 * @param string $page Page name to render and return.
	 * @return string The parsed template file.
	 */
	public function render($page)
	{
		$engine = $this->getAttribute(self::ATTR_RENDER_ENGINE);
		if (empty($engine)) {
			$engine = 'SiTech\Template\Renderer\PHP';
			require_once(\str_replace(array('_', '\\'), \DIRECTORY_SEPARATOR, $engine).'.php');
		}

		if ($this->getAttribute(self::ATTR_STRICT)) {
			$error_reporting = \error_reporting(\E_ALL);
		} else {
			// Turn off notices if strict mode is disabled.
			$error_reporting = \error_reporting(\E_ALL ^ \E_NOTICE ^ \E_USER_NOTICE);
		}

		$rendered = \call_user_func_array(array($engine, 'render'), array($this, $page, $this->_path, $this->_vars));

		\error_reporting($error_reporting);
		return($rendered);
	}

	/**
	 * Set an attribute for the current template.
	 *
	 * @param int $attr SiTech_Template::ATTR_* constant.
	 * @param mixed $value Value of attribute.
	 */
	public function setAttribute($attr, $value)
	{
		$this->_attributes[$attr] = $value;
	}


	/**
	 * Set a layout to use for the current template. If no layout is set, the
	 * template itself will still display.
	 *
	 * @param string $layout
	 * @return void
	 */
	public function setLayout($layout)
	{
		$this->_layout = $layout;
	}

	/**
	 * Remove a variable from the template.
	 *
	 * @param string $name Name of variable.
	 * @return void
	 */
	public function unassign($name)
	{
		if (isset($this->_vars[$name])) {
			unset($this->_vars[$name]);
		}
	}
}
