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
 *
 * @filesource
 */

namespace SiTech\Session\Handler;

const FILE = 'SiTech\Session\Handler\File';

/**
 * @see SiTech\Session\Handler\IHandler
 */
require_once('SiTech/Session/Handler/IHandler.php');
/**
 * @see SiTech\Session
 */
require_once('SiTech/Session.php');

/**
 * SiTech session handler for file based session storage.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Session
 * @subpackage SiTech\Session\Handler
 * @version $Id$
 */
class File implements IHandler
{
	/**
	 * Folder path to save sessions in.
	 *
	 * @var string
	 */
	protected $_savePath;

	/**
	 * Close the session.
	 *
	 * @return bool
	 */
	public function close ()
	{
		return(true);
	}

	/**
	 * Delete the session entierly.
	 *
	 * @param string $id
	 * @return bool
	 */
	public function destroy ($id)
	{
		$file = $this->_savePath.\DIRECTORY_SEPARATOR.'sess_'.$id;
		return(@\unlink($file));
	}

	/**
	 * Do garbage cleanup.
	 *
	 * @return bool
	 */
	public function gc ($maxLife)
	{
		foreach (\glob($this->_savePath.\DIRECTORY_SEPARATOR.'sess_*') as $file) {
			if (\filemtime($file) + $maxLife < \time()) {
                $fp = @\fopen($file);
				if (\is_resource($file)) {
					$r = \trim(@\fgets($fp, 4));
					@\fclose($fp);
					if ($r == '0') {
						@\unlink($file);
					}
				}
			}
		}

		return(true);
	}

	/**
	 * Open the session.
	 *
	 * @param string $path
	 * @param string $name
	 * @return bool
	 */
	public function open ($path, $name)
	{
		if (empty($path)) { $path = '/tmp'; }
		$this->_savePath = \realpath($path);
		$session = \SiTech\Session::singleton();
		$session->setAttribute(\SiTech\Session::ATTR_SESSION_NAME, $name);
		return(true);
	}

	/**
	 * Read the session information.
	 *
	 * @param string $id
	 * @return string
	 */
	public function read ($id)
	{
		$file = $this->_savePath.\DIRECTORY_SEPARATOR.'sess_'.$id;

		if (\file_exists($file) && ($fp = \fopen($file, 'r')) !== false) {
			$time = \microtime();
			$data = '';
			$str = '';
			$session = \SiTech\Session::singleton();
			$timeout = $session->getAttribute(\SiTech\Session::ATTR_FILE_TIMEOUT);

			do {
				$canRead = \flock($fp, \LOCK_SH + \LOCK_NB);
			} while (!$canRead && (\microtime() - $time) < $timeout);

			if (!$canRead) {
				\fclose($fp);
				return('');
			}

			while (!\feof($fp) && $str !== false) {
				$str = \fread($fp, 1024);
				if ($str) {
					$data .= $str;
				}
			}
			\flock($fp, \LOCK_UN);
			\fclose($fp);

			list($r, $s, $data) = \explode("\n", $data, 3);

			$session->setAttribute(\SiTech\Session::ATTR_REMEMBER, (bool)$r);
			$session->setAttribute(\SiTech\Session::ATTR_STRICT, (bool)$s);

			return((string)$data);
		} else {
			return('');
		}
	}

	/**
	 * Write the session information.
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
	public function write ($id, $data)
	{
		$file = 'sess_'.$id;
		$file = $this->_savePath.\DIRECTORY_SEPARATOR.$file;

		$session = \SiTech\Session::singleton();
		$data = \sprintf("%d\n%d\n%s", $session->getAttribute(\SiTech\Session::ATTR_REMEMBER), $session->getAttribute(\SiTech\Session::ATTR_STRICT), $data);

		if (($fp = \fopen($file, 'a')) !== false) {
			$time = \microtime();
			$session = \SiTech\Session::singleton();
			$timeout = $session->getAttribute(\SiTech\Session::ATTR_FILE_TIMEOUT);

			do {
				$canWrite = \flock($fp, \LOCK_EX + \LOCK_NB);
			} while (!$canWrite && (\microtime() - $time) < $timeout);

			if (!$canWrite) {
				\fclose($fp);
				return(false);
			}

			\ftruncate($fp, 0);
			\fputs($fp, $data);
			\flock($fp, \LOCK_UN);
			\fclose($fp);

			return(true);
		} else {
			return(false);
		}
	}
}
