<?php
/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace SiTech\Session;

/**
 * @see SiTech\Session\Exception
 */
require_once('SiTech/Session/Exception.php');

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

/**
 * This is the name of the session itself. If no name is specified, it will
 * default to the PHP setting for session.name
 */
const ATTR_SESSION_NAME = 4;

/**
 * If set to true, this will cause the session to be remembered across browser
 * sessions. This overrides the cookie default and makes the session GC handler
 * skip over the session.
 */
const ATTR_REMEMBER = 5;

/**
 * If set to true, this will cause the session to limit itself to only be
 * available to the same IP it was initally started from. If the clients IP
 * changes, the session will become invalid.
 */
const ATTR_STRICT = 6;

const FLASH_MESSAGE = 1;
const FLASH_DEBUG = 2;
const FLASH_ERROR = 3;

/**
 * The session has entered the started state.
 */
const STATE_STARTED = 1;

/**
 * The session has entered the closed state.
 */
const STATE_CLOSED = 2;

/**
 * The session has been destroyed and all data for it removed.
 */
const STATE_DESTROYED = 4;

/**
 * This is the base class for the sessions. It defines all the core usage that
 * can be extended or used for session management. This class extends the
 * ArrayObject class from SPL to add basic array functionality.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Session
 * @version $Id$
 */
class Base extends \ArrayObject
{
	/**
	 * Array of attributes that are set with the specific instance.
	 *
	 * @var array
	 */
	protected $_attributes = array();

	/**
	 * This is the handler for the session itself. It can only be set through
	 * the start() method which then calls the constructor with the handler. If
	 * no handler is set, the default file handler will be used.
	 *
	 * @var SiTech\Session\Handler\IHandler
	 */
	protected $_handler;

	/**
	 * Instance holder for the session object. This will be used when starting
	 * the session and when using the singleton.
	 *
	 * @var SiTech\Session\Base
	 */
	protected static $_instance;

	/**
	 * The way to tell if the constructor is being called internally or not. The
	 * only way to set this is from the start() method of this class.
	 *
	 * @var bool
	 */
	private static $_internal = false;

	/**
	 * Current state of the session.
	 *
	 * @see STATE_READY
	 * @var int
	 */
	protected static $_state = 0;

	/**
	 * Here we setup everything we need for the session and then call the
	 * session_start() method to get it started. After that is called, the
	 * object is then put into $_SESSION for easier access. Initally the
	 * COOKIE_DOMAIN and COOKIE_PATH attributes are set here, but can still be
	 * changed later.
	 *
	 * @param SiTech\Session\Handler\IHandler $handler The session data handler.
	 */
	public function __construct(\SiTech\Session\Handler\IHandler $handler = null)
	{
		if (!self::$_internal) {
			throw new Exception('Unable to create a session instance, please call the %s::start() method');
		}

		static::$_instance = $this;

		// If the HTTP_HOST is defined, use it as the cookie domain.
		if (isset($_SERVER['HTTP_HOST'])) {
			$this->setAttribute(ATTR_COOKIE_DOMAIN, $_SERVER['HTTP_HOST']);
		}

		// If the path prefix is defined, use it as the cookie path.
		if (defined('SITECH_PATH_PREFIX')) {
			$this->setAttribute(ATTR_COOKIE_PATH, \SITECH_PATH_PREFIX);
		} else {
			$this->setAttribute(ATTR_COOKIE_PATH, '/');
		}

		// If no handler is defined, create one using the File handler.
		if (empty($handler)) {
			require_once('SiTech/Session/Handler/File.php');
			$handler = new \SiTech\Session\Handler\File();
		}

		// Setup the save handler for the session
		$this->_handler = $handler;
		\session_set_save_handler(
			array($this->_handler, 'open'),
			array($this->_handler, 'close'),
			array($this->_handler, 'read'),
			array($this->_handler, 'write'),
			array($this->_handler, 'destroy'),
			array($this->_handler, 'gc')
		);

		// Now that we've set the handler, start the session
		\session_start();
		static::$_state = static::$_state | STATE_STARTED;
		parent::__construct($_SESSION, \ArrayObject::ARRAY_AS_PROPS);
		$_SESSION = $this;
	}

	/**
	 * The destructor here closes the session. This is done if the object is
	 * destroyed.
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * Close and write the session out using the handler. Once this is called
	 * the session will be closed.
	 *
	 * @return bool Returns false on failure.
	 */
	public function close()
	{
		if (!(static::$_state & STATE_STARTED)) {
			return(false);
		}

		//Backup the object
		$_SESSION = $this->getArrayCopy();
		// This triggers the session to be written to the storage handler
		\session_write_close();
		self::$_state = self::$_state | STATE_CLOSED;
		$_SESSION = $this;
		return(true);
	}

	/**
	 * This signals the session to destroy the session data, then sets a cookie
	 * for the session id that should cause the browser to loose the cookie.
	 *
	 * @return bool Returns false on failure.
	 */
	public function destroy()
	{
		if (static::$_state & STATE_DESTROYED) {
			return(false);
		}

		\session_destroy();
		static::$_state = static::$_state | STATE_DESTROYED;
		$cookie = \session_get_cookie_params();
		\setcookie(\session_name(), false, 419666400, $cookie['path'], $cookie['domain'], $cookie['secure']);
		// We call this in case the session.use_trans_sid is set
		\output_reset_rewrite_vars();
		return(true);
	}

	public function flash($key, $msg = null, $type =  FLASH_MESSAGE)
	{
		if (empty($msg)) {
			if (isset($_SESSION['flash']) && isset($_SESSION['flash'][$type]) && !empty($_SESSION['flash'][$type][$key])) {
				$msg = $_SESSION['flash'][$type][$key];
				$_SESSION['flash'][$type][$key] = null;
				return($msg);
			} else {
				return(false);
			}
		} else {
			if (!isset($_SESSION['flash'])) $_SESSION['flash'] = array($type => array());
			elseif (!isset($_SESSION['flash'][$type])) $_SESSION['flash'][$type] = array();

			$_SESSION['flash'][$type][$key] = $msg;
		}
	}

	/**
	 * Get the value of an attribute that is set for the specific instance.
	 *
	 * @param int $attribute Attribute to get value of
	 * @return mixed Returns null if the attribute is not set
	 */
	public function getAttribute($attribute)
	{

		$value = (isset($this->_attributes[$attribute]))? $this->_attributes[$attribute] : null;
		switch ($attribute) {
			case ATTR_COOKIE_DOMAIN:
			case ATTR_COOKIE_PATH:
			case ATTR_SESSION_NAME:
				$value = (string)$value;
				break;

			case ATTR_COOKIE_TIME:
				$value = (int)$value;
				break;

			case ATTR_REMEMBER:
			case ATTR_STRICT:
				$value = (bool)$value;
				break;
		}

		return($value);
	}

	/**
	 * Set an attribute for the instance of the class. If it is a cookie
	 * attribute or the session name, a call to setcookie() is made to update
	 * the cookie itself.
	 *
	 * @param int $attribute Attribute to set the value of
	 * @param mixed $value Value to set for attribute
	 */
	public function setAttribute($attribute, $value)
	{
		$this->_attributes[$attribute] = $value;

		switch ($attribute) {
			case ATTR_COOKIE_DOMAIN:
			case ATTR_COOKIE_PATH:
			case ATTR_COOKIE_TIME:
				if (static::$_state & STATE_STARTED) {
					setcookie(
						$this->getAttribute(ATTR_SESSION_NAME),
						session_id(),
						$this->getAttribute(ATTR_COOKIE_TIME),
						$this->getAttribute(ATTR_COOKIE_PATH),
						$this->getAttribute(ATTR_COOKIE_DOMAIN)
					);
				}
				break;

			case ATTR_REMEMBER:
				if ($value === true) {
					// Lasts for a year... good enough I'd say.
					$this->setAttribute(ATTR_COOKIE_TIME, (\time() + (86400 * 365)));
				} else {
					// Cookie should only last for the browser session.
					$this->setAttribute(ATTR_COOKIE_TIME, \time());
				}
				break;
		}
	}

	/**
	 * Singleton to retreive the instance of the session. If the session has
	 * not been started, we will attempt to start it.
	 *
	 * @return SiTech\Session\Base
	 */
	public static function singleton()
	{
		if (static::$_state & ~STATE_STARTED && self::$_internal === false) {
			static::start(); // Attempt to start the session if it isn't
		}

		return(static::$_instance);
	}

	/**
	 * Start the session! This is similar to using session_start() except it
	 * does more. We initalize the session object and return it here. The
	 * constructor will also assign the object to the $_SESSION variable so that
	 * it can be accessed elsewhere. If session_start() is used, the features of
	 * this class will not be available.
	 *
	 * @param SiTech\Session\Handler\IHandler $handler Handler interface object
	 * @return SiTech\Session\Base
	 */
	public static function start(\SiTech\Session\Handler\IHandler $handler = null)
	{
		if (isset($_SESSION)) {
			if ($_SESSION instanceof Base) {
				throw new AlreadyStartedException('Session is already started using SiTech\Session\Base. Please use SiTech\Session\Base::singleton() to get an instance of the session object.');
			} else {
				throw new AlreadyStartedException('Cannot start session, session has already been started somewhere else. Please check for session_start() or session.auto-start in your php.ini');
			}
		}

		self::$_internal = true;
		new Base($handler);
		self::$_internal = false;
		return(static::$_instance);
	}

	protected function _typeToString($type) {
		$string = 'Invalid Type';

		switch ($type) {
			case FLASH_MESSAGE:
				$string = 'FLASH_MESSAGE';
				break;
			case FLASH_DEBUG:
				$string = 'FLASH_DEBUG';
				break;
		}

		return($string);
	}
}
