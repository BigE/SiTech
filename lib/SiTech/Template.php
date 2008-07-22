<?php
/**
 * Contains the template engine for SiTech.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_Template
 * @version $Id$
 */

/**
 * SiTech_Template - This is the template class for all templates. Here you can
 * assign variables, render the page, and even display the full output.
 *
 * @package SiTech_Template
 */
class SiTech_Template
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
	protected $attributes = array();

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
	public function __construct($page, $path = null)
	{
		$this->page = $page;
		if (!empty($path)) {
			$this->path = realpath($path);
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
	public function display()
	{
		echo $this->render();
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
	 * This calls the rendering engine to parse the template file and return
	 * the complete output.
	 *
	 * @return string The parsed template file.
	 */
	public function render()
	{
		$engine = $this->getAttribute(self::ATTR_RENDER_ENGINE);
		if (empty($engine)) {
			$engine = 'SiTech_Template_Renderer_PHP';
			require_once(str_replace('_', DIRECTORY_SEPARATOR, $engine).'.php');
		}

		if ($this->getAttribute(self::ATTR_STRICT)) {
			$error_reporting = error_reporting(E_ALL);
		} else {
			$error_reporting = error_reporting(E_ALL ^ E_NOTICE);
		}

		if (!($rendered = call_user_func_array(array($engine, 'render'), array($this->page, $this->path, $this->vars)))) {
			$this->_handleError(call_user_func(array($engine, 'getError')));
		}

		error_reporting($error_reporting);
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
	 * @todo Change this to actually use the ATTR_ERRMOD setting. Currently only
	 *       exceptions are thrown.
	 */
	protected function _handleError($msg)
	{
		throw new Exception($msg);
	}
}
