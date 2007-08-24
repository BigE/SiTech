<?php
/**
 * Base excepion file for the SiTech backend.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech
 */

/**
 * Base exception for the SiTech backend. All exception classes in
 * the backend should extend this class. Also, all exceptions must
 * be included by using require_once() so that we don't get double
 * thrown exceptions.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Exception
 * @package SiTech
 */
class SiTech_Exception extends Exception
{
	public function __construct($message, array $args=array(), $code=0)
	{
		parent::__construct(vsprintf($message, $args), $code);
	}
}
?>