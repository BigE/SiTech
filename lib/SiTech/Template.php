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
 * This is the template class for all templates. Here you can
 * assign variables, render the page, and even display the full output.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2011
 * @filesource
 * @package SiTech\Template
 * @version $Id$
 */
class Template
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
     * Error mode to use for templates.
     */
    const ATTR_ERRMODE = 2;

	/**
	 * Don't display any errors to the screen.
	 */
    const ERRMODE_NONE = 0;

	/**
	 * Display a warning to the screen when an error occurs.
	 */
	const ERRMODE_WARNING = 1;

	/**
	 * Throw an exception when an error occurs.
	 */
	const ERRMODE_EXCEPTION = 2;

	/**
	 * Attributes set within the current template.
	 *
	 * @var array
	 */
	protected $attributes = array();

	protected $_layout;

	/**
	 * Template page to render.
	 *
	 * @var string
	 */
	protected $page;

	/**
	 * Template path to locate files at.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Variables set for the current template
	 *
	 * @var array
	 */
	protected $vars = array();

	/**
	 * Initalize the class by setting the page and path settings.
	 *
	 * @param string $page Page name to render.
	 * @param string $path Path where to load the template file.
	 */
	public function __construct($path = null, array $options = array())
	{
		if (!empty($path)) {
			$this->path = \realpath($path);
		}

		if (!empty($options)) {
			foreach ($options as $attr => $value) {
				$this->setAttribute($attr, $value);
			}
		}
	}

	public function __get($name)
	{
		if (isset($this->vars[$name])) {
			return($this->vars[$name]);
		} else {
			return(null);
		}
	}

	/**
	 * Assign a variable to the current template file.
	 *
	 * @param string $name Name of variable to be assigned.
	 * @param mixed $value Value of variable to be used in template.
	 * @return bool Returns TRUE on success FALSE on failure.
	 */
	public function assign($name, $val)
	{
		if ((bool)$this->getAttribute(self::ATTR_STRICT) && isset($this->vars[$name])) {
			$this->_handleError('Cannot overwrite previously set template variable '.$name.' due to strict restrictions');
			return(false);
		}

		$this->vars[$name] = $val;
		return(true);
	}

	/**
	 * Output the current template file. This just echos the output of the
	 * render method.
	 *
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
	 * Return the proper doctype tag for use in HTML documents.
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
	 * Get the value of the specified attribute. If the attribute is not set
	 * NULL will be returned.
	 *
	 * @param int $attr SiTech_Template::ATTR_* constant.
	 * @return mixed
	 */
	public function getAttribute($attr)
	{
		if (isset($this->attributes[$attr])) {
			return($this->attributes[$attr]);
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
		return($this->_layout);
	}

	/**
	 * This calls the rendering engine to parse the template file and return
	 * the complete output.
	 *
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
			$error_reporting = \error_reporting(\E_ALL ^ \E_NOTICE);
		}

		$rendered = \call_user_func_array(array($engine, 'render'), array($this, $page, $this->path, $this->vars));
		if ($rendered === false) {
			$this->_handleError(\call_user_func(array($engine, 'getError')));
		}

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
		$this->attributes[$attr] = $value;
	}


	/**
	 * Set a layout to use for the current template. If no layout is set, the
	 * template itself will still display.
	 *
	 * @param string $layout
	 */
	public function setLayout($layout)
	{
		$this->_layout = $layout;
	}

	/**
	 * Remove a variable from the template.
	 *
	 * @param string $name Name of variable.
	 */
	public function unassign($name)
	{
		if (isset($this->vars[$name])) {
			unset($this->vars[$name]);
		}
	}

	/**
	 * Handle the error according to the error output settings.
	 *
	 * @param string $msg Error message to be used.
	 */
	public function _handleError($msg, $array = array())
	{
		if ($this->getAttribute(self::ATTR_ERRMODE) === self::ERRMODE_EXCEPTION) {
			throw new Template\Exception(\vsprintf($msg, $array));
		} elseif ($this->getAttribute(self::ATTR_ERRMODE) === self::ERRMODE_WARNING) {
			\trigger_error(\vsprintf($msg, $array), \E_USER_WARNING);
		}

		$this->_error = \vsprintf($msg, $array);
	}
}

namespace SiTech\Template;
require_once('SiTech/Exception.php');
class Exception extends \SiTech\Exception {}
