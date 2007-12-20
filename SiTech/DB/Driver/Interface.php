<?php
interface SiTech_DB_Driver_Interface
{
	/**
	 * Start a transactional state with the database. Will return false on
	 * failure or if feature is unsupported.
	 *
	 * @return bool
	 */
	public function beginTransaction();
	
	/**
	 * Commit all outstanding transactions to the database. This finalizes the
	 * changes made.
	 *
	 * @return bool
	 */
	public function commit();
	
	/**
	 * Execute a SQL query on the database and return the number of rows
	 * affected.
	 *
	 * @return int
	 */
	public function exec($sql, $params = array());
	
	/**
	 * Get the value of the specified attribute.
	 *
	 * @param int $attributes
	 * @return mixed
	 */
	public function getAttribute($attributes);
	
	/**
	 * Return the error number generated by the last statement executed. Will
	 * return 0 if no previous error is found.
	 * 
	 * @return int
	 */
	public function getErrno();
	
	/**
	 * Get the last error returned byt he db server.
	 *
	 * @return array
	 */
	public function getError();
	
	/**
	 * Get the ID from the last insert statement. The column name to get the ID from
	 * can also be specified.
	 *
	 * @param string $column
	 * @return mixed
	 */
	public function getLastInsertId($column = null);
	
	/**
	 * Prepare a SQL statement for execution.
	 *
	 * @param string $sql
	 * @return SiTech_DB_Statement_Interface
	 */
	public function prepare($sql);
	
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
	public function query($sql, $fetchMode = null, $arg1 = null, $arg2 = null);
	
	/**
	 * Quote a string for use with the database based on the type specified.
	 *
	 * @param mixed $string Value to be quoted
	 * @param int $paramType SiTech_DB::TYPE_* constant
	 */
	public function quote($string, $paramType=SiTech_DB::TYPE_STRING);
	
	/**
	 * Roll back all changes made by the current transaction.
	 *
	 * @return bool
	 */
	public function rollBack();
	
	/**
	 * Set an attribute for the current connection.
	 *
	 * @param int $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attribute, $value);
	
	/**
	 * Set the default fetch mode for the current connection.
	 *
	 * @param int $mode Fetch mode to set current connection to.
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @return bool
	 */
	public function setFetchMode($mode, $arg1=null, $arg2=null);
}
