<?php
/**
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

namespace SiTech\Session\Handler;

const MEMCACHE = 'SiTech\Session\Handler\Memcache';

/**
 * @see SiTech\Session\Handler\IHandler
 */
require_once('SiTech/Session/Handler/IHandler.php');
/**
 * @see SiTech\Session
 */
require_once('SiTech/Session.php');

if (!extension_loaded('memcache')) {
	throw new Exception('You must have the memcache extension to use this handler.');
}

/**
 * SiTech session handler for memcache based session storage.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2011
 * @filesource
 * @package SiTech\Session
 * @subpackage SiTech\Session\Handler
 * @version $Id$
 */
class Memcache implements IHandler
{
	/**
	 *
	 * @var Memcache
	 */
	protected $_memcache;

	public function __construct(\Memcache $memcache = null)
	{
		if (empty($memcache) && \ini_get('session.save_handler') == 'memcache') {
			$memcache = new \Memcache();
			$memcache->connect(\ini_get('session.save_path'));
		} elseif (empty($memcache) || $memcache->getversion() === false) {
			throw new Exception('Cannot auto detect memcache settings. Please send an already connected object to this handler.');
		}

		// By default memchache sets the save_handler to memcache
		\ini_set('session.save_handler', 'user');
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
	public function open($path, $name)
	{
		return(true);
	}

	/**
	 *
	 * @param string $id Session ID
	 * @return string
	 */
	public function read($id)
	{
		if (($data = $this->_memcache->get('session/'.$id)) !== false) {
			list($r, $s, $data) = \explode("\n", $data);
			$session = \SiTech\Session::singleton();
			$session->setAttribute(\SiTech\Session::ATTR_REMEMBER, (bool)$r);
			$session->setAttribute(\SiTech\Session::ATTR_STRICT, (bool)$s);
		}

		return((string)$data);
	}

	public function write($id, $data)
	{
		$session = \SiTech\Session::singleton();
		$data = \sprintf("%d\n%d\n%s", $session->getAttribute(\SiTech\Session::ATTR_REMEMBER), $session->getAttribute(\SiTech\Session::ATTR_STRICT), $data);
		return($this->_memcache->set('session/'.$id, $data));
	}
}
