<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MemCache
 *
 * @author eric
 */
class SiTech_Session_Handler_Memcache extends Memcache
{
	public function __construct()
	{
		ini_set('session.save_handler', 'SiTech_Session_Handler_Memcache');
	}

	public function get($keys, $flags = null)
	{
		$data = parent::get($keys, $flags);
		if (is_array($data)) {
			array_walk($data, array($this, '_parseData'));
		} else {
			$this->_parseData($data);
		}

		return($data);
	}

	public function set($key, $var, $flag = null, $expire = null)
	{
		$session = SiTech_Session::singleton();
		$var = sprintf("%d\n%d\n%s", $session->getAttribute(SiTech_Session::ATTR_REMEMBER), $session->getAttribute(SiTech_Session::ATTR_STRICT), $var);
		return(parent::set($key, $var, $flag, $expire));
	}

	protected function _parseData(&$data, $key = null)
	{
		list($r, $s, $data) = explode("\n", $data, 3);
		$session = SiTech_Session::singleton();
		$session->setAttribute(SiTech_Session::ATTR_REMEMBER, (bool)$r);
		$session->setAttribute(SiTech_Session::ATTR_STRICT, (bool)$s);
	}
}
