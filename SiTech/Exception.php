<?php
/**
 * SiTech Exception class
 *
 * @package SiTech_Exception
 */

/**
 * Base exception class for SiTech library.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Exception
 * @package SiTech_Exception
 */
class SiTech_Exception extends Exception
{
	/**
	 * Exception constructor. The behavior is changed from PHP's default Exception
	 * class to make the second argument contain arguments to be used to replace
	 * sprintf style syntax in the Exception message.
	 *
	 * @param string $msg sprintf formatted string of exception message.
	 * @param array $args Array of arguments to use in string.
	 */
	public function __construct($msg, $args=array())
	{
		/*$args = func_get_args();
		array_shift($args);*/
		parent::__construct(vsprintf($msg, $args));
	}
}
?>
