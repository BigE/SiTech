<?php
/**
 * MySQL driver file for database backend.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * @see SiTech
 */
require_once('SiTech.php');

/**
 * @see SiTech_DB_Driver_Base
 */
SiTech::loadClass('SiTech_DB_Driver_Base');

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
	 * Retreive the last set error code from the server.
	 *
	 * @return string
	 */
	public function errorCode()
	{
		return(mysql_errno($this->_conn));
	}

	/**
	 * Return error information from the server.
	 *
	 * @return array Array containing error string and number.
	 */
	public function errorInfo()
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
	public function lastInsertId($name=null)
	{
		if (empty($name)) {
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
		$stmnt = new SiTech_DB_Statment_MySQL($sql);
		$stmnt->setFetchMode($this->_fetch['Mode'], $this->_fetch['Arg1'], $this->_fetch['Arg2']);
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
			case SiTech_DB::TYPE_STRING:
			default:
				$string = mysql_real_escape_string($string, $this->_conn);
				break;
		}

		return($string);
	}

	/**
	 * Rollback changes made to the database.
	 *
	 * @return bool Returns false if an error occured.
	 */
	public function rollBack()
	{
		return($this->exec('ROLLBACK'));
	}

	protected function _connect()
	{
		if (($this->_conn = @mysql_connect($this->_config['host'], $this->_config['user'], $this->_config['pass'])) === false) {
			/* TODO: Throw an exception */
		}

		if (@mysql_select_db($this->_config['db'], $this->_conn) === false) {
			/* TODO: Throw an exception */
		}
	}

	protected function _disconnect()
	{
		@mysql_close($this->_conn);
		$this->_conn = null;
	}
}
?>