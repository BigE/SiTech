<?php
/**
 * Base file for database drivers.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * @see SiTech_DB_Driver_Interface
 */
require_once('SiTech/DB/Driver/Interface.php');

/**
 * Base driver for database backend.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Driver_Base
 * @package SiTech_DB
 */
abstract class SiTech_DB_Driver_Base implements SiTech_DB_Driver_Interface
{
	/**
	 * Array holder for all attributes
	 *
	 * @var array
	 */
	protected $_attributes = array();
	
	/**
	 * Array holder for current configuration
	 *
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * Database connection holder.
	 *
	 * @var resource
	 */
	protected $_conn;
	
	public function __construct(array $config, array $options = array())
	{
		$this->_config = $config;
		foreach ($options as $attribute => $value) {
			$this->setAttribute($attribute, $value);
		}
	}
	
	/**
	 * Execute a SQL query on the database and return the number of rows
	 * affected.
	 *
	 * @param string $sql
	 * @param array $params
	 * @return int
	 */
	public function exec($sql, $params = array())
	{
		$stmnt = $this->prepare($sql);
		$stmnt->execute($params);
		return($stmnt->rowCount());
	}
	
	/**
	 * Get the value of the specified attribute. An unsuccessful call to
	 * this 
	 *
	 * @param int $attributes
	 * @return mixed
	 */
	public function getAttribute($attributes)
	{
		if (isset($this->_attributes[$attributes])) {
			return($this->_attributes[$attributes]);
		} else {
			return(null);
		}
	}

	/**
	 * Execute a SQL query on the database and return a new instance of
	 * SiTech_DB_Statement_Interface.
	 *
	 * @param string $sql
	 * @param int $fetchMode
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @return SiTech_DB_Statement_Interface
	 */
	public function query($sql, $fetchMode = null, $arg1 = null, $arg2 = null)
	{
		$stmnt = $this->prepare($sql);
		$stmnt->setFetchMode($fetchMode, $arg1, $arg2);
		$stmnt->execute();
		return($stmnt);
	}
	
	/**
	 * Set an attribute for the current connection.
	 *
	 * @param int $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attribute, $value)
	{
		switch ($attribute) {
			case SiTech_DB::ATTR_AUTOCOMMIT:
			case SiTech_DB::ATTR_PREFETCH:
			case SiTech_DB::ATTR_TIMEOUT:
			case SiTech_DB::ATTR_ERRMODE:
			case SiTech_DB::ATTR_SERVER_VERSION:
			case SiTech_DB::ATTR_CLIENT_VERSION:
			case SiTech_DB::ATTR_SERVER_INFO:
			case SiTech_DB::ATTR_CONNECTION_STATUS:
			case SiTech_DB::ATTR_CASE:
			case SiTech_DB::ATTR_CURSOR_NAME:
			case SiTech_DB::ATTR_CURSOR:
			case SiTech_DB::ATTR_ORACLE_NULLS:
			case SiTech_DB::ATTR_PERSISTENT:
			case SiTech_DB::ATTR_STATEMENT_CLASS:
			case SiTech_DB::ATTR_FETCH_TABLE_NAMES:
			case SiTech_DB::ATTR_FETCH_CATALOG_NAMES:
			case SiTech_DB::ATTR_DRIVER_NAME:
			case SiTech_DB::ATTR_STRINGIFY_FETCHES:
			case SiTech_DB::ATTR_MAX_COLUMN_LEN:
			case SiTech_DB::ATTR_EMULATE_PREPARES:
			case SiTech_DB::ATTR_DEFAULT_FETCH_MODE:
				$this->_attributes[$attribute] = $value;
				break;
			
			default:
				return(false);
				break;
		}
		
		return(true);
	}
	
	protected function _handleError($sqlState, $errno, $error)
	{
		$errMode = $this->getAttribute(SiTech_DB::ATTR_ERRMODE);
		if ($errMode === SiTech_DB::ERRMODE_EXCEPTION) {
			require_once('SiTech/DB/Exception.php');
			throw new SiTech_DB_Exception($sqlState, $errno, $error);
		} elseif ($errMode === SiTech_DB::ERRMODE_WARNING) {
			trigger_error(sprintf('%s: (%d) %s', $sqlState, $errno, $error), E_USER_WARNING);
		}
	}
	
	/**
	 * Enter description here...
	 */
	abstract protected function _connect();
	
	/**
	 * Enter description here...
	 */
	abstract protected function _disconnect();
}
