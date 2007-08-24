<?php
/**
 * Main support file for the SiTech backend. Almost every file should
 * include this file.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech
 */

/**
 * Main support class for the SiTech backend. All methods contained in
 * this class should be defined as static.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech
 * @package SiTech
 */
class SiTech
{
	/**
	 * Check if a file on the include_path setting is readable or not.
	 *
	 * @param string $file Filename to check. This can also include a partial
	 *                     or full path.
	 * @return bool Return false if file is unreadable.
	 */
	static public function isReadable($file)
	{
		if (($fp = @fopen($file, 'r', true)) !== false) {
			@fclose($fp);
			return(true);
		}
		return(false);
	}

	/**
	 * Load a class into the current runtime. If the class is not found
	 * an exception will be thrown.
	 *
	 * @param string $class Name of class to load.
	 * @throws SiTech_Exception
	 */
	static public function loadClass($class)
	{
		if (class_exists($class)) {
			return;
		}

		$file = str_replace('_', '/', $class).'.php';
		self::loadFile($file);
		if (!class_exists($class)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('Failed to load class %s', array($class));
		}
	}

	/**
	 * Load the specified file. If the file cannot be read, an exception
	 * will be thrown.
	 *
	 * @param string $file Filename to load.
	 * @param bool $once Set to false to allow more than one instance.
	 * @throws SiTech_Exception
	 */
	static public function loadFile($file, $once=true)
	{
		if (!self::isReadable($file)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('Failed to include file %s - Could not read the file.', array($file));
		}

		if ($once) {
			require_once($file);
		} else {
			require($file);
		}
	}

	/**
	 * Load a interface for a class. If the interface cannot be loaded,
	 * then an exception will be thrown.
	 *
	 * @param string $interface Interface to load.
	 * @throws SiTech_Exception
	 */
	static public function loadInterface($interface)
	{
		if (interface_exists($interface)) {
			return;
		}

		$file = str_replace('_', '/', $interface).'.php';
		self::loadFile($file);
		if (!interface_exists($interface)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('Failed to load interface %s', array($interface));
		}
	}
}
?>