<?php
/**
 * Contains the session handler.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Session
 * @version $Id$
 */

/**
 * SiTech_Session
 *
 * Base session handler class.
 *
 * @package SiTech_Session
 */
class SiTech_Session
{
	const HANDLER_DB = 'SiTech_Session_Handler_DB';
	const HANDLER_FILE = 'SiTech_Session_Handler_File';

	/**
	 * Session handler backend. This should be an object of a class that
	 * implements SiTech_Session_Handler_Interface
	 *
	 * @var object SiTech_Session_Handler_Interface
	 */
	protected $handler;

	public function __construct()
	{
		session_start();
		/* Assign the object to $_SESSION */
		$_SESSION = $this;
	}

	public function __destruct()
	{
		$this->close();
	}

	public function close()
	{
		session_write_close();
	}
}
