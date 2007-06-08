<?php
/**
 * SiTech database class
 *
 * @package SiTech_DB
 * @version $Id$
 */

/**
 * @see SiTech
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_Factory');

/**
 * Database backend that extends PDO to add some extra functionality not currently found
 * in the PDO classes.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB
 * @package SiTech_DB
 */
class SiTech_DB extends SiTech_Factory
{
	const PARAM_BOOL = 5;
	const PARAM_NULL = 0;
	const PARAM_INT = 1;
	const PARAM_STR = 2;
	const PARAM_LOB = 3;
	const PARAM_STMT = 4;
	const PARAM_INPUT_OUTPUT = -2147483648;
	const PARAM_EVT_ALLOC = 0;
	const PARAM_EVT_FREE = 1;
	const PARAM_EVT_EXEC_PRE = 2;
	const PARAM_EVT_EXEC_POST = 3;
	const PARAM_EVT_FETCH_PRE = 4;
	const PARAM_EVT_FETCH_POST = 5;
	const PARAM_EVT_NORMALIZE = 6;
	const FETCH_LAZY = 1;
	const FETCH_ASSOC = 2;
	const FETCH_NUM = 3;
	const FETCH_BOTH = 4;
	const FETCH_OBJ = 5;
	const FETCH_BOUND = 6;
	const FETCH_COLUMN = 7;
	const FETCH_CLASS = 8;
	const FETCH_INTO = 9;
	const FETCH_FUNC = 10;
	const FETCH_GROUP = 65536;
	const FETCH_UNIQUE = 196608;
	const FETCH_CLASSTYPE = 262144;
	const FETCH_SERIALIZE = 524288;
	const FETCH_PROPS_LATE = 1048576;
	const FETCH_NAMED = 11;
	const ATTR_AUTOCOMMIT = 0;
	const ATTR_PREFETCH = 1;
	const ATTR_TIMEOUT = 2;
	const ATTR_ERRMODE = 3;
	const ATTR_SERVER_VERSION = 4;
	const ATTR_CLIENT_VERSION = 5;
	const ATTR_SERVER_INFO = 6;
	const ATTR_CONNECTION_STATUS = 7;
	const ATTR_CASE = 8;
	const ATTR_CURSOR_NAME = 9;
	const ATTR_CURSOR = 10;
	const ATTR_ORACLE_NULLS = 11;
	const ATTR_PERSISTENT = 12;
	const ATTR_STATEMENT_CLASS = 13;
	const ATTR_FETCH_TABLE_NAMES = 14;
	const ATTR_FETCH_CATALOG_NAMES = 15;
	const ATTR_DRIVER_NAME = 16;
	const ATTR_STRINGIFY_FETCHES = 17;
	const ATTR_MAX_COLUMN_LEN = 18;
	const ATTR_EMULATE_PREPARES = 20;
	const ATTR_DEFAULT_FETCH_MODE = 19;
	const ERRMODE_SILENT = 0;
	const ERRMODE_WARNING = 1;
	const ERRMODE_EXCEPTION = 2;
	const CASE_NATURAL = 0;
	const CASE_LOWER = 2;
	const CASE_UPPER = 1;
	const NULL_NATURAL = 0;
	const NULL_EMPTY_STRING = 1;
	const NULL_TO_STRING = 2;
	const ERR_NONE = 00000;
	const FETCH_ORI_NEXT = 0;
	const FETCH_ORI_PRIOR = 1;
	const FETCH_ORI_FIRST = 2;
	const FETCH_ORI_LAST = 3;
	const FETCH_ORI_ABS = 4;
	const FETCH_ORI_REL = 5;
	const CURSOR_FWDONLY = 0;
	const CURSOR_SCROLL = 1;
	const PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT = 1000;
	const MYSQL_ATTR_USE_BUFFERED_QUERY = 1000;
	const MYSQL_ATTR_LOCAL_INFILE = 1001;
	const MYSQL_ATTR_INIT_COMMAND = 1002;
	const MYSQL_ATTR_READ_DEFAULT_FILE = 1003;
	const MYSQL_ATTR_READ_DEFAULT_GROUP = 1004;
	const MYSQL_ATTR_MAX_BUFFER_SIZE = 1005;
	const MYSQL_ATTR_DIRECT_QUERY = 1006;

	private $_dsn = array();

	static private $_interface;

	/**
	 * We have to declare our own constructor to support the getInterface() method within
	 * this class.
	 *
	 * @access public
	 * @param string $dsn DSN string to be passed to PDO
	 * @param string $username Username to use with the connection
	 * @param string $password Password to use with the connection
	 * @param array $driver_options Array of driver options to use with PDO
	 */
	public function __construct($dsn, $driverOptions=array())
	{
		if (!is_string($dsn)) {
			SiTech::loadClass('SiTech_DB_Exception');
			throw new SiTech_DB_Exception('%s::%s() expects first argument to be string', array(__CLASS__, __METHOD__));
		}

		self::$_interface = $this;
		$class = 'SiTech_DB_';

		switch (strtolower($this->_dsn['type'])) {
			case 'mysql':
				$class .= 'MySQL';
				break;

			case 'pdo_mysql':
				$class .= 'PDO_MySQL';
				break;

			case 'pdo_pgsql':
				$class .= 'PDO_PGSQL';
				break;

			case 'pdo_sqlite':
				$class .= 'PDO_SQLite';
				break;

			default:
				$class .= $this->_dsn['type'];
				break;
		}

		SiTech::loadClass($class);
		$this->_backend = new $class($this->_dsn);
	}

	/**
	 * Get the last interface initalied for use. If you're using multiple interfaces, this
	 * most likely won't be useful.
	 *
	 * @access public
	 * @param string $dsn DSN string to be passed to PDO
	 * @param string $username Username to use with the connection
	 * @param string $password Password to use with the connection
	 * @param array $driver_options Array of driver options to use with PDO
	 * @return SiTech_DB
	 */
	static public function getInterface($dsn=null)
	{
		if (!(self::$_interface instanceof SiTech_DB)) {
			self::$_interface = new SiTech_DB($dsn, $username, $password, $driver_options);
		}

		return(self::$_interface);
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
