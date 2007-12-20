<?php
/**
 * MySQL driver file for database backend.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * @see SiTech_DB_Driver_Base
 */
require_once('SiTech/DB/Driver/Base.php');

/**
 * MySQL driver for database backend.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Driver_MySQL
 * @package SiTech_DB
 */
class SiTech_DB_Driver_MySQL extends SiTech_DB_Driver_Base
{
	/**
	 * Begin a SQL transaction. This will not work on MyISAM
	 * databases, but only database types that support transactions. Please
	 * see http://dev.mysql.com/doc/refman/5.1/en/transactional-commands.html
	 *
	 * @return bool Returns false if there was an error.
	 */
	public function beginTransaction()
	{
		return((bool)$this->exec('BEGIN'));
	}

	/**
	 * Commit current transaction set. This will not work on MyISAM
	 * databases, but only database types that support transactions. Please
	 * see http://dev.mysql.com/doc/refman/5.1/en/transactional-commands.html
	 *
	 * @return bool Returns false if there was an error.
	 */
	public function commit()
	{
		return((bool)$this->exec('COMMIT'));
	}

	/**
	 * Retreive the last set error code from the server.
	 *
	 * @return string
	 */
	public function getErrno()
	{
		return(mysql_errno($this->_conn));
	}

	/**
	 * Return error information from the server.
	 *
	 * @return array Array containing error string and number.
	 */
	public function getError()
	{
		return(
			array(
				'',
				mysql_errno($this->_conn),
				mysql_error($this->_conn)
			)
		);
	}

	/**
	 * Retreive the ID of the last row inserted into the database.
	 *
	 * @param string $name Field name to grab ID from.
	 */
	public function getLastInsertId($column=null)
	{
		if (empty($column)) {
			return(mysql_insert_id($this->_conn));
		} else {
			/* TODO: Implement functionality to grab ID based on field name. */
			return(null);
		}
	}

	/**
	 * Prepare a string of SQL for execution with the database.
	 *
	 * @param string $sql SQL to prepare.
	 * @return SiTech_DB_Statement_Interface Return a statement with the prepared SQL.
	 */
	public function prepare($sql)
	{
		$this->_connect();
		require_once('SiTech/DB/Statement/MySQL.php');
		$stmnt = new SiTech_DB_Statement_MySQL($sql, $this->_conn, $this->_attributes);
		$stmnt->setFetchMode($this->_fetchMode['mode'], $this->_fetchMode['arg1'], $this->_fetchMode['arg2']);
		return($stmnt);
	}

	/**
	 * Quote a string for use with the database based on the type specified.
	 *
	 * @param mixed $string Value to be quoted
	 * @param int $paramType SiTech_DB::TYPE_* constant
	 */
	public function quote($string, $paramType=SiTech_DB::TYPE_STRING)
	{
		switch ($paramType) {
			case SiTech_DB::TYPE_TABLE:
				$string = "`$string`";
				break;
				
			case SiTech_DB::TYPE_STRING:
			default:
				$string = mysql_real_escape_string($string, $this->_conn);
				break;
		}

		return($string);
	}

	/**
	 * Rollback changes made to the database. This will not work on MyISAM
	 * databases, but only database types that support transactions. Please
	 * see http://dev.mysql.com/doc/refman/5.1/en/transactional-commands.html
	 *
	 * @return bool Returns false if an error occured.
	 */
	public function rollBack()
	{
		return((bool)$this->exec('ROLLBACK'));
	}

	protected function _connect()
	{
		if (is_resource($this->_conn)) return;
		
		if (($this->_conn = @mysql_connect($this->_config['host'], $this->_config['user'], $this->_config['pass'])) === false) {
			$this->_handleError('', mysql_errno(), mysql_error());
		}

		if (@mysql_select_db($this->_config['db'], $this->_conn) === false) {
			$this->_handleError('', mysql_errno($this->_conn), mysql_error($this->_conn));
		}
	}

	protected function _disconnect()
	{
		@mysql_close($this->_conn);
		$this->_conn = null;
	}
}
