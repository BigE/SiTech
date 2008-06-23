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
 * SiTech_Session - A wrapper for sessions that uses $_SESSION
 *
 * We use a very different method of session handling with this class. It extends
 * ArrayObject to make access to session variables much easier to access, and
 * it also turns the $_SESSION superglobal into an object of SiTech_Session.
 *
 * @package SiTech_Session
 */
class SiTech_Session extends ArrayObject
{
	/**
	 * This is the cookie domain for the session. The attribute value should be
	 * set to a string.
	 */
	const ATTR_COOKIE_DOMAIN = 1;

	/**
	 * This is the cookie path for the session. The attribute value should be
	 * set to a string.
	 */
	const ATTR_COOKIE_PATH = 2;

	/**
	 * This is the time that the cookie will expire in. The attribute value
	 * should be set to an int that is represented by seconds.
	 */
	const ATTR_COOKIE_TIME = 3;

	const ATTR_DB_CONN = 4;

	const ATTR_DB_TABLE = 5;

	const ATTR_SESSION_NAME = 6;

	const ATTR_REMEMBER = 7;

	const ATTR_STRICT = 8;

	/**
	 * Database handler for session.
	 */
	const HANDLER_DB = 'SiTech_Session_Handler_DB';

	/**
	 * File handler for session.
	 */
	const HANDLER_FILE = 'SiTech_Session_Handler_File';

	const STATE_STARTED = 1;

	const STATE_CLOSED = 2;

	const STATE_DESTROYED = 4;

	/**
	 * The array stores each attribute value.
	 *
	 * @see setAttribute(),getAttribute()
	 * @var array Attribute storage array.
	 */
	protected $attributes = array();

	/**
	 * Session handler backend. This should be an object of a class that
	 * implements SiTech_Session_Handler_Interface
	 *
	 * @var object SiTech_Session_Handler_Interface
	 */
	protected $handler;

	protected $state;

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

	/**
	 * Close the session and save it. This will save the session data and close
	 * it.
	 *
	 * @return bool Returns TRUE on success, FALSE on failure.
	 */
	public function close()
	{
		if (!$this->isActive()) {
			return;
		}

		$obj = $this;
		$array = $this->getArrayCopy();
		$_SESSION = $array;
		session_write_close();
		$this->state = $this->state | self::STATE_CLOSED;
		$_SESSION = $obj;
	}

	/**
	 * Destroy the session completely. This clears the cookie as well as the
	 * session variables.
	 *
	 * @return bool Returns TRUE on success, FALSE on failure.
	 */
	public function destroy()
	{
		if ($this->isDestroyed()) {
			return;
		}

		session_destroy();
		$this->state = $this->state | self::STATE_DESTROYED;
		$cookie = session_get_cookie_params();
		setcookie(session_name(), false, 419666400, $cookie['path'], $cookie['domain'], $cookie['secure']);
		output_reset_rewrite_vars();
	}

	/**
	 * Returns the current value of the attribute specified. See the
	 * SiTech_Session::ATTR_* constants for attributes.
	 *
	 * @param int $attr Attribute to get.
	 * @return mixed Returns value of attribute, null when the attribute is not set.
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
	 * Check to see if the current session is active or not.
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return((bool)$this->state & self::STATE_STARTED && !$this->isDestroyed() && !$this->isClosed());
	}

	/**
	 * Check to see if the session is closed or not.
	 *
	 * @return bool
	 */
	public function isClosed()
	{
		return((bool)$this->state & self::STATE_CLOSED);
	}

	/**
	 * Check to see if the current session has been destroyed.
	 *
	 * @return bool
	 */
	public function isDestroyed()
	{
		return((bool)$this->state & self::STATE_DESTROYED);
	}

	/**
	 * Set an attribute for the session. See the SiTech_Session::ATTR_* constants
	 * for specific attributes.
	 *
	 * @param int $attr Attribute to set.
	 * @param mixed $value Value to set attribute to.
	 * @return bool Returns TRUE on success, FALSE on failure.
	 */
	public function setAttribute($attr, $value)
	{
		switch ($attr) {
			case self::ATTR_COOKIE_DOMAIN:
			case self::ATTR_COOKIE_PATH:
			case self::ATTR_DB_TABLE:
			case self::ATTR_SESSION_NAME:
				$this->attributes[$attr] = (string)$value;
				break;

			case self::ATTR_COOKIE_TIME:
				$this->attributes[$attr] = (int)$value;
				break;

			case self::ATTR_DB_CONN:
				if (!is_object($value) || (!is_a($value, 'SiTech_DB') && !is_subclass_of($value, 'SiTech_DB'))) {
					throw new Exception('Invalid class for database connection, object must be a sub class or the main class of SiTech_DB');
				}

				$this->attributes[$attr] = $value;
				break;

			case self::ATTR_REMEMBER:
			case self::ATTR_STRICT:
				$this->attributes[$attr] = (bool)$value;
				break;

			default:
				return(false);
				break;
		}

		return(true);
	}
}
