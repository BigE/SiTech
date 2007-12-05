<?php
/**
 * @see SiTech
 */
require_once ('SiTech.php');

/**
 * @see SiTech_DB_Statement_Base
 */
SiTech::loadClass('SiTech_DB_Statement_Base');

/**
 *
 */
class SiTech_DB_Statement_PGSQL extends SiTech_DB_Statement_Base
{

	/**
	 * 
	 * @see SiTech_DB_Statement_Interface::closeCursor()
	 */
	public function closeCursor ()
	{
		return(@pg_free_result($this->_result));
	}

	/**
	 * 
	 * @see SiTech_DB_Statement_Interface::columnCount()
	 */
	public function columnCount ()
	{
		return(@pg_num_fields($this->_result));
	}

	/**
	 * 
	 * @see SiTech_DB_Statement_Interface::errorCode()
	 */
	public function errorCode ()
	{
		if (pg_result_error_field($this->_result, PGSQL_DIAG_SQLSTATE) == null) {
			return(0);
		} else {
			return(-1);
		}
	}

	/**
	 * 
	 * @see SiTech_DB_Statement_Interface::errorInfo()
	 */
	public function errorInfo ()
	{
		return(
			array(
				pg_result_error_field($this->_result, PGSQL_DIAG_SQLSTATE),
				$this->errorCode(),
				pg_result_error_field($this->_result, PGSQL_DIAG_MESSAGE_PRIMARY)
			)
		);
	}

	/**
	 * 
	 * @see SiTech_DB_Statement_Interface::getColumnMeta()
	 */
	public function getColumnMeta ($column)
	{
		$info = array();

		if (($info['type'] = pg_field_type($this->_result, $column)) === false) {
			return(false);
		}

		/* not sure if this is possible */
		$info['flags'] = null;

		if (($info['len'] = pg_field_prtlen($this->_result, $column)) === false) {
			return(false);
		}

		if (($info['name'] = pg_field_name($this->_result, $column)) === false) {
			return(false);
		}

		$info['seek'] = null;

		if (($info['table'] = pg_field_table($this->_result, $column)) === false) {
			return(false);
		}

		return($info);
	}

	/**
	 * 
	 * @see SiTech_DB_Statement_Interface::rowCount()
	 */
	public function rowCount ()
	{
		if (($rows = pg_num_rows($this->_result)) === false) {
			return(pg_affected_rows($this->_result));
		} else {
			return($rows);
		}
	}
	
	/**
	 * Bind a column to a PHP variable.
	 *
	 * @param mixed $column Column to bind variable to.
	 * @param mixed $var Variable to bind to column.
	 * @param int $type Force type on variable.
	 * @return bool Returns false on failure.
	 */
	protected function _bindColumn($column, &$var, $type)
	{
		/* Nothing specific to do for this driver */
		return(true);
	}

	/**
	 * Bind a parameter to the specified variable.
	 *
	 * @param mixed $parameter Parameter to bind variable to.
	 * @param mixed $var Variable to bind to parameter.
	 * @param int $type Force type specified on variable.
	 * @param int $length Force length on variable.
	 * @param array $driverOptions Other driver options to specify for this parameter.
	 * @return bool Returns false on failure.
	 * @todo Add parameter checking to ensure success
	 */
	protected function _bindParam($parameter, &$var, $type, $length, $driverOptions)
	{
		/* Nothing specific to do for this driver */
		return(true);
	}
	
	/**
	 * Execute the prepared statement.
	 *
	 * @return bool
	 */
	protected function _execute(array $params=array())
	{
		if (pg_send_query($this->_sql, $this->_conn) === false) {
			list($sqlState, $errno, $error) = $this->_errorInfo();
			$this->_handleError($sqlState, $errno, $error);
			return(false);
		}

		$this->_result = pg_get_result($this->_conn);
		return(true);
	}
	
	protected function _fetch($mode, $arg1=null, $arg2=null)
	{
		switch ($mode) {
			case SiTech_DB::FETCH_ASSOC:
				$row = pg_fetch_assoc($this->_result);
				break;

			case SiTech_DB::FETCH_BOTH:
				$row = pg_fetch_array($this->_result);
				break;

			case SiTech_DB::FETCH_COLUMN:
				$row = pg_fetch_field($this->_result, $arg1);
				break;

			case SiTech_DB::FETCH_NUM:
				$row = pg_fetch_row($this->_result);
				break;

			case SiTech_DB::FETCH_OBJ:
				$row = pg_fetch_object($this->_result, $arg1, $arg2);
				break;
		}

		return($row);
	}
}
?>
