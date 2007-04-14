<?php
interface SiTech_Session_Interface
{
	public function close();
	public function destroy();
	public function setCookieDomain($domain);
	public function setCookiePath($path);
	public function setCookieTime($seconds);
	public function setName($name);
	public function setStrict($strict);
	public function start();
	/**
	 * These have to be public because PHP will call them, though they must never
	 * be called directly in code.
	 */
	public function _close();
	public function _destroy($id);
	public function _gc($maxLife);
	public function _open($path, $name);
	public function _read($id);
	public function _write($id, $data);
}
?>