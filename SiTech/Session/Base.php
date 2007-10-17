<?php
require_once('SiTech.php');
SiTech::loadInterface('SiTech_Session_Interface');
SiTech::loadClass('SiTech_Session');

/**
 *
 */
abstract class SiTech_Session_Base implements SiTech_Session_Interface
{
	protected $_attributes = array();
	
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
		session_write_close();
	}

	/**
	 * 
	 * @see SiTech_Session_Interface::destroy()
	 * @return bool
	 */
	public function destroy ()
	{
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
	 * @see SiTech_Session_Interface::start()
	 */
	public function start ()
	{
	}
	
	abstract protected function _close();
	abstract protected function _destroy($id);
	abstract protected function _gc($maxLife);
	abstract protected function _open($path, $name);
	abstract protected function _read($id);
	abstract protected function _write($id, $data);
}
?>
