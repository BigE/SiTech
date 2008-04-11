<?php
/**
 * MySQL support file for database statements.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * @see SiTech_DB
 */
require_once('SiTech/DB.php');

/**
 * @see SiTech_DB_Statement_Base
 */
require_once('SiTech/DB/Statement/Base.php');

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
	 * Return the SQLSTATE of the last operation executed.
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
		if (($rows = @mysql_num_rows($this->_result)) === false) {
			return(@mysql_affected_rows($this->_conn));
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
	protected function _bindColumn($column, &$var, $type=null)
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
	protected function _bindParam($parameter, &$var, $type, $length, array $driverOptions)
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
		$sql = $this->_sql;
		$params = array_merge($params, $this->_boundParams);
		foreach ($params as $param => $value) {
			if ($param[0] == ':') {
				$value = mysql_escape_string($value);
				$sql = str_replace($param, "'$value'", $sql);
				unset($params[$param]);
			}
		}

		if (!empty($params)) {
			$inQuote = false;
			$tmp = '';
			reset($params);
			for ($x = 0; $x < strlen($sql); $x++) {
				if ($sql[$x] == '?' && !$inQuote) {
					$value = current($params);
					$tmp .= '\''.mysql_real_escape_string($value).'\'';
					next($params);
				} elseif ($sql[$x] == '\'' && !$inQuote) {
					$inQuote = true;
					$tmp .= $sql[$x];
				} elseif ($sql[$x] == '\\' && $inQuote) {
					$tmp .= $sql[$x];
					$x++;
					$tmp .= $sql[$x];
				} else {
					$tmp .= $sql[$x];
				}
			}
			$sql = $tmp;
		}

		if (($result = mysql_query($sql, $this->_conn)) === false) {
			$this->_handleError('', mysql_errno(), mysql_error());
			return(false);
		}

		$this->_result = $result;
		return(true);
	}

	protected function _fetch($mode, $arg1=null, $arg2=null)
	{
		switch ($mode) {
			case SiTech_DB::FETCH_ASSOC:
			default:
				$row = mysql_fetch_assoc($this->_result);
				break;

			case SiTech_DB::FETCH_BOTH:
				$row = mysql_fetch_array($this->_result);
				break;

			case SiTech_DB::FETCH_COLUMN:
				$row = mysql_fetch_field($this->_result, $arg1);
				break;

			case SiTech_DB::FETCH_NUM:
				$row = mysql_fetch_row($this->_result);
				break;

			case SiTech_DB::FETCH_OBJ:
			case SiTech_DB::FETCH_CLASS:
				$row = mysql_fetch_object($this->_result, $arg1, $arg2);
				break;
		}

		return($row);
	}

	/**
	 * Prepare SQL for execution.
	 *
	 * @param string $sql
	 * @return string
	 */
	public function _prepareSql($sql)
	{
		return($sql);
	}
}
?>
