<?php
/**
 * Utilities available to the SiTech library.
 */

/**
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_Utilitiy
 */
interface SiTech_Request_Interface {
	static public function getRequestData($var, $method=null, $type=null);
	
	static public function requestMethod();
	
	static public function validRequestData($var, $method=null);
}
?>
