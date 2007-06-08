<?php
/**
 * SiTech Session Base
 *
 * @package SiTech_Session
 * @version $Id$
 */

/**
 * Get the SiTech base class.
 */
require_once('SiTech.php');
SiTech::loadInterface('SiTech_Session_Interface');

/**
 * SiTech Base support for Sessions. All the base (common) functionality is
 * included here in this file.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Session_Base
 * @package SiTech_Session
 */
abstract class SiTech_Session_Base implements SiTech_Session_Interface
{
	protected $_cookie = array(
		'domain'	=> null,
		'path'		=> '/',
		'time'		=> 0 /* this defaults to close when the browser closes */
	);

	protected $_id;

	protected $_name;

	protected $_remember = false;

	protected $_strict = false;

	/**
	 * Base class constructor. Here we set the session handlers.
	 */
	public function __construct()
	{
		/* Defined in the classes that extend this class */
		session_set_save_handler(
			array($this, '_open'),
			array($this, '_close'),
			array($this, '_read'),
			array($this, '_write'),
			array($this, '_destroy'),
			array($this, '_gc')
		);
	}

	/**
	 * Check for our custom variables.
	 *
	 * @param string $var Variable name.
	 */
	public function __get($var)
	{
		switch ($var) {
			case 'id':
				return($this->_id);
				break;

			case 'name':
				return($this->_name);
				break;

			case 'remember':
				return($this->_remember);
				break;

			default:
				return($this->$var);
				break;
		}
	}

	/**
	 * Set a class variable. We check for custom variables here.
	 *
	 * @param string $var Variable name.
	 * @param mixed $val Variable value.
	 */
	public function __set($var, $val)
	{
		switch ($var)
		{
			case 'remember':
				$this->setRemember($val);
				break;

			default:
				break;
		}
	}

	/**
	 * Close a session. This will store the data saved in the session.
	 *
	 * @return void
	 */
	public function close()
	{
		session_write_close();
	}

	/**
	 * Destroy the current session. This removes all session data from existance.
	 *
	 * @return bool
	 */
	public function destroy()
	{
		return(session_destroy());
	}

	/**
	 * Set the cookie domain for the session cookie.
	 *
	 * @param string $domain Domain for session cookie.
	 */
	public function setCookieDomain($domain)
	{
		$this->_cookie['domain'] = $domain;
	}

	/**
	 * Set the cookie path for the session cookie.
	 *
	 * @param string $path Session path for the cookie.
	 */
	public function setCookiePath($path)
	{
		$this->_cookie['path'] = $path;
	}

	/**
	 * Set the cookie time for how long the cookie should last.
	 *
	 * @param int $seconds Time in seconds for how long cookie should last.
	 */
	public function setCookieTime($seconds)
	{
		$this->_cookie['time'] = (int)$seconds;
	}

	/**
	 * Set the session name.
	 *
	 * @param string $name Session name.
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}

	/**
	 * If set to true, we remember the session even through GC. This also
	 * changes the cookie time to a year so that the user is not forgotten
	 * eaisly.
	 *
	 * @param bool $remember True to turn remember on, false to turn it off.
	 */
	public function setRemember($remember)
	{
		$r = (bool)$remember;
		if ($r) {
			$this->_cookie['time'] = 31449600; /* default one year */
			$this->_remember = true;
		} else {
			$this->_cookie['time'] = 0;
			$this->_remember = false;
		}
	}

	/**
	 * Set strict options. This enables checks against the IP address to make
	 * the session more secure.
	 *
	 * @param bool $strict True to turn on, false to disable.
	 */
	public function setStrict($strict)
	{
		if ($this->_started) {
			$_SESSION['__SiTech_REMOTE_ADDR__'] = $_SERVER['REMOTE_ADDR'];
		}

		$this->_strict = (bool)$strict;
	}

	/**
	 * Start the session using the default values for the session.
	 *
	 * @return bool
	 */
	public function start()
	{
		session_set_cookie_params($this->_cookie['time'], $this->_cookie['path'], $this->_cookie['name']);
		$retVal = session_start();

		if ($retVal) {
			if ($this->_strict) {
				$_SESSION['__SiTech_REMOTE_ADDR__'] = $_SERVER['REMOTE_ADDR'];
			}

			$this->_started = true;
		}

		return($retVal);
	}
}
?>
