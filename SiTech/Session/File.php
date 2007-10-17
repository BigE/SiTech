<?php
require_once ('SiTech/Session/Base.php');
require_once ('SiTech/Session/Interface.php');
/**
 *
 */
class SiTech_Session_File extends SiTech_Session_Base
{
	/**
	 * Path to where sessions are stored.
	 *
	 * @var string
	 */
	protected $_savePath;

	/**
	 * Close the session.
	 * 
	 * @return bool
	 */
	protected function _close ()
	{
		return(true);
	}

	/**
	 * Delete the session entierly.
	 * 
	 * @param string $id
	 * @return bool
	 */
	protected function _destroy ($id)
	{
		$file = 'sess_'.$id;
		$file = realpath($this->_savePath.DIRECTORY_SEPARATOR.$file);
		return(@unlink($file));
	}

	/**
	 * Do garbage cleanup.
	 * 
	 * @return bool
	 */
	protected function _gc ($maxLife)
	{
		foreach (glob($this->_savePath.DIRECTORY_SEPARATOR.'sess_*') as $file) {
			if (filemtime($this->_savePath.DIRECTORY_SEPARATOR.$file) + $maxLife < time()) {
				if (($fp = @fopen($this->_savePath.DIRECTORY_SEPARATOR.$file)) !== false) {
					$r = trim(@fgets($fp, 4));
					@fclose($fp);
					if ($r == '0') {
						@unlink($this->_savePath.DIRECTORY_SEPARATOR.$file);
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
	protected function _open ($path, $name)
	{
		$this->_savePath = $path;
		$this->setAttribute(SiTech_Session::ATTR_NAME, $name);
		return(true);
	}

	/**
	 * Read the session information.
	 * 
	 * @param string $id
	 * @return string
	 */
	protected function _read ($id)
	{
		$file = 'sess_'.$id;
		$file = realpath($this->_savePath.DIRECTORY_SEPARATOR.$file);
		
		if (file_exists($file)) {
			$data = @file_get_contents($file);
			list($r, $s, $data) = explode("\n", $data, 3);
			
			$this->setAttribute(SiTech_Session::ATTR_REMEMBER, (bool)$r);
			$this->setAttribute(SiTech_Session::ATTR_STRICT, (bool)$s);
			
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
	protected function _write ($id, $data)
	{
		$file = 'sess_'.$id;
		$file = realpath($this->_savePath.DIRECTORY_SEPARATOR.$file);
		
		$data = sprintf("%d\n%d\n%s", $this->getAttribute(SiTech_Session::ATTR_REMEMBER), $this->getAttribute(SiTech_Session::ATTR_STRICT), $data);
		
		if (is_writeable($file)) {
			@file_put_contents($file, $data);
			return(true);
		} else {
			return(false);
		}
	}
}
?>
