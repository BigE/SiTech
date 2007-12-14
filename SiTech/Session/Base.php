<?php
/**
 * 
 */

/**
 * @see SiTech_Session
 */
require_once('SiTech/Session.php');

/**
 * @see SiTech_Session_Interface
 */
require_once ('SiTech/Session/Interface.php');

/**
 * @author Eric Gach <eric.gach@gmail.com>
 */
abstract class SiTech_Session_Base implements SiTech_Session_Interface
{
	protected $_attributes = array();
	
	protected $_started = false;
	
	/**
	 * 
	 */
	public function __construct ()
	{
		/* set default values */
		$this->setAttribute(SiTech_Session::ATTR_COOKIE_DOMAIN, $_SERVER['HTTP_HOST']);
		$this->setAttribute(SiTech_Session::ATTR_COOKIE_PATH, '/');
		$this->setAttribute(SiTech_Session::ATTR_COOKIE_TIME, 3600); /* one hour */
		$this->setAttribute(SiTech_Session::ATTR_NAME, '');
		$this->setAttribute(SiTech_Session::ATTR_REMEMBER, false);
		$this->setAttribute(SiTech_Session::ATTR_STRICT, false);
		
		/* set session handlers */
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
	 * 
	 * @see SiTech_Session_Interface::close()
	 */
	public function close ()
	{
		$this->_started = false;
		session_write_close();
	}

	/**
	 * 
	 * @see SiTech_Session_Interface::destroy()
	 * @return bool
	 */
	public function destroy ()
	{
		$this->_started = false;
		return(session_destroy());
	}

	/**
	 * 
	 * @param int $attribute 
	 * @return mixed 
	 * @see SiTech_Session_Interface::getAttribute()
	 */
	public function getAttribute ($attribute)
	{
		if (isset($this->_attributes[$attribute])) {
			return($this->_attributes[$attribute]);
		} else {
			return(null);
		}
	}
	
	public function isStarted()
	{
		return($this->_started);
	}

	/**
	 * 
	 * @param int $attribute 
	 * @param mixed $value 
	 * @return bool 
	 * @see SiTech_Session_Interface::setAttribute()
	 */
	public function setAttribute ($attribute, $value)
	{
		$ret = true;
		
		switch ($attribute)
		{
			case SiTech_Session::ATTR_COOKIE_DOMAIN:
			case SiTech_Session::ATTR_COOKIE_PATH:
			case SiTech_Session::ATTR_NAME:
				$this->_attributes[$attribute] = $value;
				break;
				
			case SiTech_Session::ATTR_COOKIE_TIME:
				$this->_attributes[$attribute] = (int)$value;
				break;
			
			case SiTech_Session::ATTR_REMEMBER:
			case SiTech_Session::ATTR_STRICT:
				$this->_attributes[$attribute] = (bool)$value;
				break;
				
			default:
				$ret = false;
				break;	
		}
		
		return($ret);
	}

	/**
	 * 
	 * @return bool
	 */
	public function start ()
	{
		/* first the options need to be set */
		session_set_cookie_params(
			$this->getAttribute(SiTech_Session::ATTR_COOKIE_TIME),
			$this->getAttribute(SiTech_Session::ATTR_COOKIE_PATH),
			$this->getAttribute(SiTech_Session::ATTR_COOKIE_DOMAIN)
		);
		
		if (!empty($this->_attributes[SiTech_Session::ATTR_NAME])) {
			session_name($this->_attributes[SiTech_Session::ATTR_NAME]);
		}
		
		if (session_start()) {
			$this->_started = true;
			return(true);
		} else {
			return(false);
		}
	}
	
	abstract public function _close();
	abstract public function _destroy($id);
	abstract public function _gc($maxLife);
	abstract public function _open($path, $name);
	abstract public function _read($id);
	abstract public function _write($id, $data);
}
?>
