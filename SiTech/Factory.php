<?php
/**
 * SiTech Factory class
 *
 * @package SiTech
 * @version $Id$
 */

/**
 * This is a factory class for all of our main classes. This provides base
 * functionality that allows all other classes to be easily written by only
 * making the constructor to create the internal object.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Factory
 * @package SiTech
 */
abstract class SiTech_Factory
{
	protected $_backend;

	/**
	 * Using a little voodoo here, we call the child class method if it's defined.
	 * If it isn't, we throw an exception of the method not existing. This enables
	 * the class to function dynamically.
	 */
	public function __call($method, $args)
	{
		if (method_exists($this->_backend, $method) || method_exists($this->_backend, '__call')) {
			return(call_user_func_array(array($this->_backend, $method), $args));
		} else {
			SiTech::loadClass('SiTech_Exception');
			throw new SiTech_Exception('Tried to call unknown method '.get_class($this->_backend).'::'.$method);
		}
	}

	/**
	 * Get a variable from the child class.
	 */
	public function __get($var)
	{
		if (is_object($this->_backend) && isset($this->_backend->$var)) {
			return($this->_backend->$var);
		} else {
			return(null);
		}
	}

	/**
	 * Set a variable to the child class.
	 */
	public function __set($var, $val)
	{
		if (is_object($this->_backend)) {
			$this->_backend->$var = $val;
		}
	}
}
?>