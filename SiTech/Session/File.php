<?php
/**
 * SiTech Session File support.
 *
 * @package SiTech_Session
 * @version $Id$
 */

/**
 * Grab the main SiTech file.
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_Session_Base');

/**
 * SiTech File based Session support.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Session_File
 * @package SiTech_Session
 */
class SiTech_Session_File extends SiTech_Session_Base
{
	private $_savePath;

	/**
	 * This tells if the session is successfully closed or not. Since there's not
	 * much to check, just return true.
	 *
	 * @return bool
	 */
	public function _close()
	{
		return(true);
	}

	/**
	 * Remove the session from existance. This is done no matter what the session
	 * settings are.
	 *
	 * @param string $id Session ID
	 * @return bool
	 */
	public function _destroy($id)
	{
		$file = 'sess_'.$this->_id;
		$file = realpath($this->_savePath.'/'.$file);
		return(@unlink($file));
	}

	/**
	 * General cleanup... this is triggered by PHPs GC settings. If we find a session
	 * that is older than the max life and is not to be remembered, then we remove
	 * the session as part of the cleanup.
	 *
	 * @param int $maxLife Maximum lifetime for a session.
	 * @return bool
	 */
	public function _gc($maxLife)
	{
		foreach (glob($this->_savePath.'/sess_*') as $file) {
			if (filemtime($this->_savePath.'/'.$file) + $maxLife < time()) {
				if (($fp = @fopen($this->_savePath.'/'.$file)) !== false) {
					$r = trim(fgets($fp, 4));
					if ($r == '0') {
						@unlink($this->_savePath.'/'.$file);
					}
				}
			}
		}

		return(true);
	}

	/**
	 * Open the session. This is used when session_start() is called uppon to let
	 * PHP know that the session is ready to begin.
	 *
	 * @param string $path Path to all session files.
	 * @param string $name Session name to prepend to session ID.
	 * @return bool
	 */
	public function _open($path, $name)
	{
		$this->_savePath = $path;
		$this->_name = $name;
		return(true);
	}

	/**
	 * Read the session information in from the file. We also set custom class variables
	 * while reading the information in so that our own customizations stick.
	 *
	 * @param string $id Session id.
	 * @return string
	 */
	public function _read($id)
	{
		$file = 'sess_'.$this->_id;
		$file = realpath($this->_savePath.'/'.$file);

		if (file_exists($file)) {
			$data = @file_get_contents($file);
			list($r, $s, $data) = explode("\n", $data, 3);

			if ($r == '1') {
				$this->setRemember(true);
			} else {
				$this->setRemember(false);
			}

			if ($s == '1') {
				$this->setStrict(true);
			} else {
				$this->setStrict(false);
			}

			return((string)$data);
		} else {
			return('');
		}
	}

	/**
	 * Write the session information to the file. Our custom data is written first
	 * so that we can easily check it later when neccisary.
	 *
	 * @param string $id Session id.
	 * @param string $data Session data to save.
	 * @return bool
	 */
	public function _write($id, $data)
	{
		$file = 'sess_'.$this->_id;
		$file = realpath($this->_savePath.'/'.$file);

		if ($this->_strict) {
			$data = "1\n$data";
		} else {
			$data = "0\n$data";
		}

		if ($this->_remember) {
			$data = "1\n$data";
		} else {
			$data = "0\n$data";
		}

		if (!file_exists($file)) {
			touch($file);
		}

		if (is_writeable($file)) {
			@file_put_contents($file, $data);
		} else {
			return(false);
		}
	}
}
?>
