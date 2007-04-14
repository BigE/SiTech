<?php
/**
 * SiTech database class
 *
 * @package SiTech_DB
 */

/**
 * SiTech base functionality
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_Factory');

/**
 * Database backend to interface to all sorts of databases and making management
 * of data easier. Currently supported is the MySQL database.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB
 * @package SiTech_DB
 */
class SiTech_DB extends SiTech_Factory
{
	/**
	 * Array holder for parsed dsn string.
	 *
	 * @var array
	 */
	private $_dsn = array();

	/**
	 * Class constructor for database backend.
	 *
	 * @param string $dsn DSN style string specifying database information.
	 * @param array $options Array of options for database.
	 */
	public function __construct($dsn, $options=array())
	{
		/* parse up the dsn */
		$this->_dsn = self::parseDsn($dsn);

		$class = 'SiTech_DB_';
		switch (strtolower($this->_dsn['type'])) {
			case 'mysql':
				$class .= 'MySQL';
				break;

			default:
				$class = $this->_dsn['type'];
				break;
		}

		SiTech::loadClass($class);
		$this->_backend = new $class($this->_dsn);
	}

	/**
	 * Parse a DSN formatted string and return the results in an array. If the
	 * string is not a valid DSN array, and exception will be thrown.
	 *
	 * @param string $dsn DSN formatted string to parse.
	 * @return array
	 */
	static public function parseDsn($dsn)
	{
		$parsed = array(
			'dbname'	=> null,
			'host'	  => null,
			'options'   => array(),
			'pass'	  => null,
			'port'	  => null,
			'protocol'  => null,
			'socket'	=> null,
			'type'	  => null,
			'user'	  => null
		);

		if (($local = @parse_url($dsn)) === false) {
			/* ruh roh */
			SiTech::loadClass('SiTech_DB_Exception');
			throw new SiTech_DB_Exception('Invalid DSN formatted string.');
		}

		$parsed = array_merge($parsed, $local);

		if (!isset($parsed['scheme'])) {
			SiTech::loadClass('SiTech_DB_Exception');
			throw new SiTech_DB_Exception('Invalid DSN formatted string.');
		}

		$parsed['type'] = $parsed['scheme'];
		unset($parsed['scheme']);

		if (isset($parsed['path'])) {
			$parsed['dbname'] = substr($parsed['path'], 1);
			unset($parsed['path']);
		}

		if (isset($parsed['query'])) {
			$parsed['query'] = explode('&', $parsed['query']);
			foreach ($parsed['query'] as $item) {
				list($key, $value) = explode('=', $item, 2);
				$parsed['options'][$key] = $value;
			}
		}

		return($parsed);
	}
}
?>
