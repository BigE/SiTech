<?php
/**
 * PostgreSQL file for database drivers.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * @see SiTech_DB_Driver_Base
 */
require_once('SiTech/DB/Driver/Base.php');

/**
 * PostgreSQL driver for database backend.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Driver_Base
 * @package SiTech_DB
 */
class SiTech_DB_Driver_PGSQL extends SiTech_DB_Driver_Base
{

	/**
	 * Begin a SQL transaction.
	 *
	 * @return bool Returns false if there was an error.
	 */
	public function beginTransaction()
	{
		return((bool)$this->exec('BEGIN'));
	}

	/**
	 * Commit current transaction set.
	 *
	 * @return bool Returns false if there was an error.
	 */
	public function commit()
	{
		return((bool)$this->exec('COMMIT'));
	}

	/**
	 * Retreive the last set error code from the server. In PGSQL's case I can't
	 * find a way to get an error code, so it returns 0 on no error and -1 if there
	 * is an error.
	 * 
	 * @return int 
	 * @todo Fix this so it actually works.
	 */
	public function getErrno ()
	{
		if (pg_last_error($this->_conn) === null) {
			return(0);
		} else {
			/* I haven't found anything that actually gives an error code for pgsql */
			return(-1);
		}
	}

	/**
	 * Return error information from the server.
	 * 
	 * @return array 
	 */
	public function getError ()
	{
		list($error,) = explode("\n", pg_last_error($this->_conn), 2);
		list(, $sqlCode, $error) = explode(': ', $error, 3);
		return(
			array(
				ltrim($sqlCode),
				$this->getErrno(),
				$error
			)
		);
	}

	/**
	 * Retreive the ID of the last row inserted into the database.
	 * 
	 * @param string $column 
	 * @return mixed 
	 */
	public function getLastInserId ($column = null)
	{
		if ($column == null) {
			return(pg_last_oid(null));
		} else {
			/* TODO: Implement functionality to grab ID based on field name. */
			return(false);
		}
	}

	/**
	 * 
	 * @param string $sql 
	 * @return SiTech_DB_Statement_Interface 
	 * @see SiTech_DB_Driver_Base::prepare()
	 */
	public function prepare ($sql)
	{
		SiTech::loadClass('SiTech_DB_Statement_PGSQL');
		$stmnt = new SiTech_DB_Statement_PGSQL($sql);
		return($stmnt);
	}

	/**
	 * 
	 * @param mixed $string Value to be quoted 
	 * @param int $paramType SiTech_DB::TYPE_* constant 
	 * @see SiTech_DB_Driver_Base::quote()
	 */
	public function quote ($string, $paramType)
	{
		switch ($paramType) {
			case SiTech_DB::TYPE_BINARY:
				$string = pg_escape_bytea($string);
				break;
				
			default:
				$string = pg_escape_string($string);
				break;
		}
		
		return($string);
	}

	/**
	 * Rollback changes made to the database.
	 * 
	 * @return bool
	 */
	public function rollBack ()
	{
		return((bool)$this->exec('ROLLBACK'));
	}
	
	/**
	 * Open a connection to the database.
	 */
	protected function __connect ()
	{
		$connStr = '';
		foreach ($this->_config as $key => $var) {
			switch ($key) {
				case 'host':
					$connStr .= "host=$var ";
					break;

				case 'db':
					$connStr .= "dbname=$var ";
					break;
					
				case 'user':
					$connStr .= "user=$var ";
					break;
					
				case 'pass':
					$connStr .= "password=$var ";
					break;
				
				case 'port':
					$var = (int)$var;
					$connStr .= "port=$var ";
					break;
			}
		}
		
		ini_set('track_errors', 1);
		$this->_conn = @pg_connect(trim($connStr));
		ini_restore('track_errors');
		
		if ($this->_conn === false) {
			$this->_handleError('', -1, $GLOBALS['php_errormsg']);
		}
	}

	/**
	 * 
	 * @see SiTech_DB_Driver_Base::__disconnect()
	 */
	protected function __disconnect ()
	{
		@pg_close($this->_conn);
		$this->_conn = null;
	}
}
?>
