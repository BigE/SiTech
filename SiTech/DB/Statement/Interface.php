<?php
interface SiTech_DB_Statement_Interface
{
	/**
	 * Bind a column to a PHP variable.
	 *
	 * @param mixed $column Column to bind variable to.
	 * @param mixed $var Variable to bind to column.
	 * @param int $type Force type on variable.
	 * @return bool Returns false on failure.
	 */
	public function bindColumn($column, &$var, $type);

	/**
	 * Bind a parameter to the specified variable.
	 *
	 * @param mixed $parameter Parameter to bind variable to.
	 * @param mixed $var Variable to bind to parameter.
	 * @param int $type Force type specified on variable.
	 * @param int $length Force length on variable.
	 * @param array $driverOptions Other driver options to specify for this parameter.
	 * @return bool Returns false on failure.
	 */
	public function bindParam($parameter, &$var, $type=SiTech_DB::PARAM_STR, $length=null, array $driverOptions=array());

	/**
	 * Bind a value to a parameter.
	 *
	 * @param mixed $parameter Parameter to bind value to.
	 * @param mixed $value Value to bind to parameter.
	 * @param int $type Force type specified on value.
	 * @return bool Returns fals on failure.
	 */
	public function bindValue($parameter, $value, $type=SiTech_DB::PARAM_STR);

	/**
	 * Close the cursor and enable the statement to be executed again.
	 *
	 * @return bool Returns false on failure.
	 */
	public function closeCursor();

	/**
	 * Return the number of columns in the result set.
	 *
	 * @return int
	 */
	public function columnCount();

	/**
	 * Return the SQLSTATE of the las operation executed.
	 *
	 * @return string
	 */
	public function errorCode();

	/**
	 * Fetch extended informtion associated witht he last operation executed.
	 *
	 * @return array
	 */
	public function errorInfo();

	/**
	 * Execute the prepared statement.
	 *
	 * @param array $params Values to assign to parameters in the SQL.
	 * @return bool
	 */
	public function execute(array $params=array());

	/**
	 * Fetch the next row from the result set.
	 *
	 * @param int $fetchMode Fetch mode to use when fetching the row.
	 * @param int $curserOrientation
	 * @param int $cursorOffset
	 * @return mixed
	 */
	public function fetch($fetchMode=null, $curserOrientation=null, $cursorOffset=null);

	/**
	 * Return an array of all the rows in the result set.
	 *
	 * @param int $fetchMode Fetch mode to use when fetching all rows.
	 * @param misc $arg1
	 * @param misc $arg2
	 * @return array
	 */
	public function fetchAll($fetchMode=null, $arg1=null, $arg2=null);

	/**
	 * Return a single column from the next row in a result set.
	 *
	 * @param int $columnNumber Column index to return.
	 * @return string
	 */
	public function fetchColumn($columnNumber=0);

	/**
	 * Fetch the next row from a result set and return it as an object.
	 *
	 * @param string $className Class to use when creating object.
	 * @param array $constructArgs Array of arguments to pass to the constructor of the new object.
	 * @return mixed
	 */
	public function fetchObject($className=null, array $constructArgs=array());

	/**
	 * Retreive the specified statement attribute.
	 *
	 * @param int $attribute Attribute to get the value of.
	 * @return mixed
	 */
	public function getAttribute($attribute);

	/**
	 * Returns information about the column specified in the result set.
	 *
	 * @param int $column Column to get information about.
	 * @return array Returns false on error
	 */
	public function getColumnMeta($column);

	/**
	 * Advance to the next result set in a multi-result call.
	 *
	 * @return bool
	 */
	public function nextRowset();

	/**
	 * Return the number of rows affected by the last SQL statement.
	 *
	 * @return int
	 */
	public function rowCount();

	/**
	 * Set an attribute for the current statement.
	 *
	 * @param int $attribute Attribute to set value to.
	 * @param mixed $value Value to set attribute to.
	 * @return bool
	 */
	public function setAttribute($attribute, $value);

	/**
	 * Set the default fetch mode for the current statement.
	 *
	 * @param int $mode Fetch mode to set current statement to.
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @return bool
	 */
	public function setFetchMode($mode, $arg1=null, $arg2=null);

	protected function _bindColumn($column, &$var, $type=null);
	protected function _bindParam($parameter, &$var, $type, $length, array $driverOptions);
	protected function _execute();
	protected function _fetch($mode, $arg1=null, $arg2=null);
	protected function _prepareSql($sql);
}
?>