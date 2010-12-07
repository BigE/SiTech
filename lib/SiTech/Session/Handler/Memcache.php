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
class SiTech_Session_Handler_Memcache implements SiTech_Session_Handler_Interface
{
	/**
	 *
	 * @var Memcache
	 */
	protected $_memcache;

	public function __construct(Memcache $memcache = null)
	{
		if (empty($memcache) && ini_get('session.save_handler') == 'memcache') {
			$memcache = new Memcache();
			$memcache->connect(ini_get('session.save_path'));
		} elseif (empty($memcache) || $memcache->getversion() === false) {
			throw new SiTech_Exception('Cannot auto detect memcache settings. Please send an already connected object to this handler.');
		}

		// By default memchache sets the save_handler to memcache
		ini_set('session.save_handler', 'user');
		$this->_memcache = $memcache;
	}

	/**
	 * Nothing to do here because we don't need to close memcache just because
	 * the session has ended.
	 *
	 * @return bool
	 */
	public function close()
	{
		return(true);
	}

	/**
	 * Destroy teh sessions!
	 *
	 * @param string $id Session ID
	 */
	public function destroy($id)
	{
		return($this->_memcache->delete('session/'.$id));
	}

	/**
	 * Cleanup for old sessions
	 *
	 * @param int $maxLife
	 */
	public function gc($maxLife)
	{
		return(true);
	}

	/**
	 * Nothing to do here because memcache should already be opened in our
	 * constructor.
	 *
	 * @return bool
	 */
	public function open()
	{
		return(true);
	}

	public function read($id)
	{
		$data = $this->_memcache->get('session/'.$id);
		list($r, $s, $data) = explode("\n", $data);
		$session = SiTech_Session::singleton();

		return($data);
	}

	public function write($id, $data)
	{
		$session = SiTech_Session::singleton();
		$data = sprintf("%d\n%d\n%s", $session->getAttribute(SiTech_Session::ATTR_REMEMBER), $session->getAttribute(SiTech_Session::ATTR_STRICT), $data);
		return($this->_memcache->set('session/'.$id, $data));
	}
}
