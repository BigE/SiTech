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

	static protected $handler = false;

	static protected $instance;

	/**
	 * Tells the constructor if the session was initalized internally or not.
	 *
	 * @var bool
	 */
	static protected $internal;

	/**
	 * Current state of the session. See the SiTech_Session::STATE_ constants.
	 *
	 * @var int
	 */
	protected $state;

	/**
	 * Constructor - We initalize the system here.
	 *
	 * @todo Fix a segfault that occurs with xdebug.
	 */
	public function __construct()
	{
		if (self::$internal == false) {
			trigger_error('Call to protected '.__METHOD__.' from invalid context', E_USER_ERROR);
		}

		self::$instance = $this;

		if (!self::$handler) {
			$handler = self::HANDLER_FILE;
			require_once(str_replace('_', '/', $handler).'.php');
			self::registerHandler(new $handler());
		}

		session_start();
		$this->state = $this->state | self::STATE_STARTED;
		parent::__construct($_SESSION, ArrayObject::ARRAY_AS_PROPS);
		/* Assign the object to $_SESSION */
		/* TODO: Find another way to do this!! This causes a segfault when using xdebug. */
		$_SESSION = $this;
	}

	/**
	 * Destructor.
	 */
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
	 * Register a session handler. This must be done before the session is started
	 * to ensure the proper methods are used.
	 *
	 * @param SiTech_Session_Handler_Interface $object An object that implements
	 *                                                 SiTech_Session_Handler_Interface.
	 * @throws Exception
	 */
	static public function registerHandler($object)
	{
		if (isset($_SESSION)) {
			throw new Exception('You cannot register a handler after the session has been started');
		} elseif (!($object instanceof SiTech_Session_Handler_Interface)) {
			throw new Exception('The session handler must implement SiTech_Session_Handler_Interface');
		}

		self::$handler = true;

		session_set_save_handler(
			array($object, 'open'),
			array($object, 'close'),
			array($object, 'read'),
			array($object, 'write'),
			array($object, 'destroy'),
			array($object, 'gc')
		);
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

	static public function singleton()
	{
		if (empty(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}

		return(self::$instance);
	}

	/**
	 * Start the session. This must be called instead of the constructor so that
	 * proper setup of the session can be acheived. Any handlers must be registered
	 * before this method is called.
	 *
	 * @throws Exception
	 */
	static public function start()
	{
		if (isset($_SESSION) && $_SESSION instanceof SiTech_Session && $_SESSION->isActive()) {
			return;
		} elseif (isset($_SESSION) && !($_SESSION instanceof Session)) {
			throw new Exception('A session has already been started using session_start() or session.auto-start');
		}

		self::$internal = true;
		self::singleton();
		self::$internal = false;
	}
}
