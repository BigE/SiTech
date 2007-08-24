<?php
/**
 * @see SiTech
 */
require_once('SiTech.php');

/**
 * @see SiTech_DB
 */
SiTech::loadClass('SiTech_DB');

/**
 * @see SiTech_DB_Statement_Base
 */
SiTech::loadClass('SiTech_DB_Statement_Base');

/**
 * Enter description here...
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Statement_MySQL
 * @package SiTech_DB
 */
class SiTech_DB_Statement_MySQL extends SiTech_DB_Statement_Base
{
	/**
	 * Close the cursor and enable the statement to be executed again.
	 *
	 * @return bool Returns false on failure.
	 */
	public function closeCursor()
	{
		return(@mysql_free_result($this->_result));
	}

	/**
	 * Return the number of columns in the result set.
	 *
	 * @return int
	 */
	public function columnCount()
	{
		return(@mysql_num_fields($this->_result));
	}

	/**
	 * Return the SQLSTATE of the las operation executed.
	 *
	 * @return string
	 * @todo Find a way to implement this with MySQL
	 */
	public function errorCode()
	{
		return(false);
	}

	/**
	 * Fetch extended informtion associated witht he last operation executed.
	 *
	 * @return array
	 */
	public function errorInfo()
	{
		return(array(
			false,
			mysql_errno($this->_conn),
			mysql_error($this->_conn)
		));
	}

	/**
	 * Execute the prepared statement.
	 *
	 * @param array $params Values to assign to parameters in the SQL.
	 * @return bool
	 */
	public function execute(array $params=array())
	{
		if (($result = mysql_query($this->_sql, $this->_conn)) === false) {
			/* TODO: Throw a new exception */
			return(false);
		}

		$this->_result = $result;
		return(true);
	}

	/**
	 * Returns information about the column specified in the result set.
	 *
	 * @param int $column Column to get information about.
	 * @return array Returns false on error
	 * @todo Implement exceptions into all calls to mysql_feild_* functions
	 */
	public function getColumnMeta($column)
	{
		$info = array();

		if (($info['type'] = mysql_field_type($this->_result, $column)) === false) {
			return(false);
		}

		if (($info['flags'] = @mysql_field_flags($this->_result, $column)) === false) {
			return(false);
		}

		if (($info['len'] = mysql_field_len($this->_result, $column)) === false) {
			return(false);
		}

		if (($info['name'] = mysql_field_name($this->_result, $column)) === false) {
			return(false);
		}

		if (($info['seek'] = mysql_field_seek($this->_result, $column)) === false) {
			return(false);
		}

		if (($info['table'] = mysql_field_table($this->_result, $column)) === false) {
			return(false);
		}

		return($info);
	}

	/**
	 * Advance to the next result set in a multi-result call.
	 *
	 * @return bool
	 * @todo Implement functionality
	 */
	public function nextRowset()
	{
		/* not implemented */
		return(false);
	}

	/**
	 * Return the number of rows affected by the last SQL statement.
	 *
	 * @return int
	 */
	public function rowCount()
	{
		if (($rows = mysql_num_rows($this->_result)) === false) {
			return(mysql_affected_rows($this->_result));
		} else {
			return($rows);
		}
	}

	protected function _fetch($mode, $arg1=null, $arg2=null)
	{
		switch ($mode) {
			case SiTech_DB::FETCH_ASSOC:
				$row = mysql_fetch_assoc($this->_result);
				break;

			case SiTech_DB::FETCH_BOTH:
				$row = mysql_fetch_array($this->_result);
				break;

			case SiTech_DB::FETCH_COLUMN:
				$row = mysql_fetch_field($this->_result, $column);
				break;

			case SiTech_DB::FETCH_NUM:
				$row = mysql_fetch_row($this->_result);
				break;

			case SiTech_DB::FETCH_OBJ:
				$row = mysql_fetch_object($this->_result, $arg1, $arg2);
				break;
		}

		return($row);
	}
}
?>