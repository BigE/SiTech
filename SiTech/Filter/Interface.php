<?php
/**
 * Variable filter available to the SiTech library.
 */

/**
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_Filter
 */
interface SiTech_Filter_Interface {
	/**
	 * Get request data by the specified method. If no method is specified, it
	 * defaults to getting it from $_REQUEST
	 *
	 * @param string $var
	 * @param int $method SiTech_Filter::METHOD_* constant
	 * @param int $type SiTech_Filter::TYPE_* constant
	 * @return mixed
	 */
	static public function getRequestData($var, $method=null, $type=null);
	
	/**
	 * Return the request method for the current request. If it is not set, this
	 * method will return null.
	 *
	 * @return string
	 */
	static public function requestMethod();
	
	/**
	 * Check if the request contains valid data for the specified variable.
	 *
	 * @param string $var
	 * @param int $method SiTech_Filter::METHOD_* constant
	 * @return bool
	 */
	static public function validRequestData($var, $method=null);
}
?>
