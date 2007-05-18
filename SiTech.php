<?php
/**
 * SiTech base class.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech
 */

/**
 * SiTech base class.
 *
 * This is our base class. All of our other classes should use this file as it provides
 * extra functionality for ease of use. This file is not too bulky, so including it everywhere
 * doesn't cost us major performance. Also, all methods are static, so no objects should
 * be created for use.
 *
 * @author  Eric Gach <eric.gach@gmail.com>
 * @package SiTech
 */
class SiTech
{
	/**
	 * Check if file is readable on include_path.
	 *
	 * This is handy for checking files within the include path. Returns true if the file is
	 * readable, and false if it is not.
	 *
	 * @param string $file Filename to check if it's readable or not.
	 * @return bool
	 */
	static public function isReadable($file)
	{
		if (($fp = @fopen($file, 'r', true)) === false) {
			return(false);
		}

		@fclose($fp);
		return(true);
	}

	/**
	 * Load a class.
	 *
	 * Load a class into the scope of the project. If loading the class fails, an exception
	 * is thrown.
	 *
	 * @param string $class Class name to load into the scope.
	 * @throws SiTech_Exception Exception
	 */
	static public function loadClass($class)
	{
		if (class_exists($class)) {
			/* No sense in trying to redeclare the class */
			return;
		}

		$file = str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
		self::loadFile($file);

		/* Make sure the class exists after loading the file */
		if (!class_exists($class)) {
			if ($class !== 'SiTech_Exception') {
				SiTech::loadClass('SiTech_Exception');
				throw new SiTech_Exception('Could not load class "%s" from include path: %s', array($class, get_include_path()));
			} else {
				/* fallback to make sure we always have an exception handler */
				throw new Exception(vsprintf('Could not load class "%s" from include path: %s', array($class, get_include_path())));
			}
		}
	}

	/**
	 * Load a file.
	 *
	 * This is pretty much useless for procedural code, but useful when it comes to classes
	 * and definitions. An exception is thrown whenever a file cannot be read or including
	 * it fails.
	 *
	 * @param string $file Filename of file to include
	 * @param bool $once True to include only once, false to include more than once.
	 * @throws SiTech_Exception
	 */
	static public function loadFile($file, $once=true)
	{
		if (!self::isReadable($file)) {
			if ($file !== 'SiTech'.DIRECTORY_SEPARATOR.'Exception.php') {
				self::loadClass('SiTech_Exception');
				throw new SiTech_Exception('Could not load file "%s" - File could not be read', array($file));
			} else {
				return;
			}
		}

		if ($once === true) {
			require_once($file);
		} else {
			require($file);
		}
	}

	/**
	 * Load an interface.
	 *
	 * Load an interface into the current scope. If the interface is not defined after loading
	 * an exception is thrown.
	 *
	 * @param string $interface Interface name to load.
	 * @throws SiTech_Exception
	 */
	static public function loadInterface($interface)
	{
		if (interface_exists($interface)) {
			return;
		}

		$file = str_replace('_', DIRECTORY_SEPARATOR, $interface).'.php';
		self::loadFile($file);

		if (!interface_exists($interface)) {
			if ($class !== 'SiTech_Exception') {
				SiTech::loadClass('SiTech_Exception');
				throw new SiTech_Exception('Could not load interface "%s" from include path: %s', array($class, get_include_path()));
			} else {
				/* fallback to make sure we always have an exception handler */
				throw new Exception(vsprintf('Could not load interface "%s" from include path: %s', array($class, get_include_path())));
			}
		}
	}
}
?>
