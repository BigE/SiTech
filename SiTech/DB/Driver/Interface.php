<?php
/**
 * Interface file for SiTech_DB_Driver_Base
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * Interface for SiTech_DB_Driver_Base. All backend classes must extend the
 * SiTech_DB_Backend_Base class so that it implements these standards.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Driver_Interface
 * @package SiTech_DB
 */
interface SiTech_DB_Driver_Interface
{
	/**
	 * Begin a SQL transaction.
	 *
	 * @return bool Returns false if there was an error.
	 */
	public function beginTransaction();

	/**
	 * Commit current transaction set.
	 *
	 * @return bool Returns false if there was an error.
	 */
	public function commit();

	/**
	 * Delete rows from the specified table.
	 *
	 * @param string $table Table name to delete rows from.
	 * @param array $where Criteria for row deletion.
	 * @return int Number of rows deleted.
	 */
	public function delete($table, array $where=array());

	/**
	 * Retreive the last set error code from the server.
	 *
	 * @return string
	 */
	public function errorCode();

	/**
	 * Return error information from the server.
	 *
	 * @return array Array containing error string and number.
	 */
	public function errorInfo();

	/**
	 * Execute a SQL statement on the server.
	 *
	 * @param string $sql SQL to execute on the server.
	 * @param array $params Parameters to bind to the SQL statement.
	 * @return int Number of rows returned by the query.
	 */
	public function exec($sql, array $params=array());

	/**
	 * Retreive an attribute set for the server.
	 *
	 * @param int $attribute SiTech_DB::PARAM_* constant of attribute to get.
	 * @return mixed Attribute's value
	 */
	public function getAttribute($attribute);

	/**
	 * Get the current fetch mode.
	 *
	 * @return int SiTech_DB::FETCH_* mode currently set.
	 */
	public function getFetchMode();

	/**
	 * Retreive the ID of the last row inserted into the database.
	 *
	 * @param string $name Field name to grab ID from.
	 */
	public function lastInsertId($name=null);

	/**
	 * Insert a new row into the database.
	 *
	 * @param string $table Table to insert rows into.
	 * @param array $values Field=>Value style array of values.
	 * @return int Number of rows inserted.
	 */
	public function insert($table, array $values);

	/**
	 * Prepare a string of SQL for execution with the database.
	 *
	 * @param string $sql SQL to prepare.
	 * @return SiTech_DB_Statement_Interface Return a statement with the prepared SQL.
	 */
	public function prepare($sql);

	/**
	 * Execute a query on the database server.
	 *
	 * @param string $sql
	 * @param int $fetchMethod SiTech_DB::FETCH_* constant
	 * @param mixed $misc1
	 * @param mixed $misc2
	 */
	public function query($sql, $fetchMethod=null, $misc1=null, $misc2=null);

	/**
	 * Quote a string for use with the database based on the type specified.
	 *
	 * @param mixed $string Value to be quoted
	 * @param int $paramType SiTech_DB::TYPE_* constant
	 */
	public function quote($string, $paramType=null);

	/**
	 * Rollback changes made to the database.
	 *
	 * @return bool Returns false if an error occured.
	 */
	public function rollBack();

	/**
	 * Return a select object to perform a select query on the database.
	 *
	 * @return SiTech_DB_Select
	 */
	public function select();

	/**
	 * Set a value to an attribute for the server.
	 *
	 * @param int $attribute SiTech_DB::PARAM_*
	 * @param mixed $value Value to set attribute to.
	 */
	public function setAttribute($attribute, $value);

	/**
	 * Set the current fetch mode and arguments for it.
	 *
	 * @param int $mode SiTech_DB::FETCH_* constant.
	 * @param mixed $arg1
	 * @param mixed $arg2
	 */
	public function setFetchMode($mode, $arg1, $arg2);

	/**
	 * Perform an update query to the database.
	 *
	 * @param string $table
	 * @param array $values Field=>Value array of values.
	 * @param array $where
	 */
	public function update($table, array $values, array $where=array());
}
?>