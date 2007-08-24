<?php
/**
 * Factory class for making child types easier to access. We don't add
 * any functionality to __get, __set, or __call allowing the child classes
 * to still define their own magic methods.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Factory
 * @package SiTech
 */
class SiTech_Factory
{
	/**
	 * Backend holder for child class.
	 *
	 * @var mixed
	 */
	protected $_objBackend;

	/**
	 * Holder for variables.
	 *
	 * @var array
	 */
	protected $_vars = array();

	/**
	 * Call a method in the child class.
	 *
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		if (method_exists($this->_objBackend, $method) || method_exists($this->_objBackend, '__call')) {
			return(call_user_method_array($method, $this->_objBackend, $args));
		} else {
			trigger_error('', E_USER_WARNING);
		}
	}

	/**
	 * Get a variable set in a class.
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function __get($var)
	{
		if (property_exists($this->_objBackend, $var) || method_exists($this->_objBackend, '__get')) {
			return($this->_objBackend->$var);
		} else {
			return($this->_vars[$var]);
		}
	}

	/**
	 * Set a class variable.
	 *
	 * @param string $var
	 * @param mixed $val
	 */
	public function __set($var, $val)
	{
		if (property_exists($this->_objBackend, $var) || method_exists($this->_objBackend, '__set')) {
			$this->_objBackend->$var = $val;
		} else {
			$this->_objBackend[$var] = $val;
		}
	}
}
?>