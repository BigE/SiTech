<?php
/**
 * Base file for database statements.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * @see SiTech
 */
require_once('SiTech.php');

/**
 * @see SiTech_DB_Statement_Interface
 */
SiTech::loadInterface('SiTech_DB_Statement_Interface');

/**
 * Base class for database statements.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Statement_Base
 * @package SiTech_DB
 */
abstract class SiTech_DB_Statement_Base implements SiTech_DB_Statement_Interface
{
	/**
	 * Current attributes set for this statement.
	 *
	 * @var array
	 */
	protected $_attributes = array();

	/**
	 * Bound columns.
	 *
	 * @var array
	 */
	protected $_boundColumns = array();

	/**
	 * Bound parameters.
	 *
	 * @var array
	 */
	protected $_boundParams = array();

	/**
	 * Connection resource.
	 *
	 * @var mixed
	 */
	protected $_conn;

	/**
	 * Fetch mode holder.
	 *
	 * @var array
	 */
	protected $_fetchMode = array('mode' => SiTech_DB::FETCH_ASSOC, 'arg1' => null, 'arg2' => null);

	/**
	 * Result holder.
	 *
	 * @var result
	 */
	protected $_result;

	/**
	 * SQL string without bound parameters.
	 *
	 * @var string
	 */
	protected $_sql;

	/**
	 * SQL Params found in SQL string.
	 *
	 * @var array
	 */
	protected $_sqlParams = array();

	/**
	 * Class constructor
	 *
	 * @param mixed $sql String or SiTech_DB_Select object.
	 * @param mixed $conn Connection resource.
	 */
	public function __construct($sql, $conn, $driverOptions=array())
	{
		if ($sql instanceof SiTech_DB_Select) {
			$sql = $sql->__toString();
		}
		
		$this->_sql = $this->_prepareSql($sql);
		$this->_parseParams($this->_sql);
		$this->_conn = $conn;
		$this->_attributes = $driverOptions;
	}

	/**
	 * Bind a column to a PHP variable.
	 *
	 * @param mixed $column Column to bind variable to.
	 * @param mixed $var Variable to bind to column.
	 * @param int $type Force type on variable.
	 * @return bool Returns false on failure.
	 */
	public function bindColumn($column, &$var, $type)
	{
		$this->_boundColumns[$column] = array(
			'variable' => &$var,
			'type' => $type
		);

		return($this->_bindColumn($column, $var, $type));
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
	public function bindParam($parameter, &$var, $type=SiTech_DB::PARAM_STR, $length=null, array $driverOptions=array())
	{
		if (!is_int($parameter) && !is_string($parameter)) {
			require_once('SiTech/DB/Exception.php');
			throw new SiTech_DB_Exception();
		}

		$position = null;
		if (($intval = intval($parameter)) > 0 && $intval <= sizeof($this->_boundParams)) {
			$position = $intval;
		} else {
			if ($parameter[0] != ':') {
				$parameter = ':'.$parameter;
			}

			if (in_array($parameter, $this->_sqlParams)) {
				$position = $parameter;
			}
		}

		if ($position === null) {
			switch ($this->getAttribute(SiTech_DB::ATTR_ERRMODE)) {
				case SiTech_DB::ERRMODE_EXCEPTION:
					require_once('SiTech/DB/Exception.php');
					throw new SiTech_DB_Exception();
					break;

				case SiTech_DB::ERRMODE_WARNING:
					trigger_error('', E_USER_WARNING);
					break;
			}

			return(false);
		}

		$this->_boundParams[$position] = array(
			'value' => $var,
			'type' => $type,
			'length' => $length,
			'driverOptions' => $driverOptions
		);

		return($this->_bindParam($var, $type, $length, $driverOptions));
	}

	/**
	 * Bind a value to a parameter.
	 *
	 * @param mixed $parameter Parameter to bind value to.
	 * @param mixed $value Value to bind to parameter.
	 * @param int $type Force type specified on value.
	 * @return bool Returns fals on failure.
	 * @todo Add parameter checking to ensure success
	 */
	public function bindValue($parameter, $value, $type=SiTech_DB::PARAM_STR)
	{
		return($this->bindParam($parameter, $value, $type));
	}

	/**
	 * Execute the prepared statement.
	 *
	 * @param array $params Values to assign to parameters in the SQL.
	 * @return bool
	 * @todo Use parameters in SQL
	 */
	public function execute(array $params=array())
	{
		return($this->_execute($params));
	}

	/**
	 * Fetch the next row from the result set.
	 *
	 * @param int $fetchMode Fetch mode to use when fetching the row.
	 * @param int $curserOrientation
	 * @param int $cursorOffset
	 * @return mixed
	 * @todo Add code to bind to variables
	 */
	public function fetch($fetchMode=null, $curserOrientation=null, $cursorOffset=null)
	{
		try {
			if (!empty($fetchMode)) {
				$oldFetchMode = $this->_fetchMode;
				$this->setFetchMode($fetchMode, $curserOrientation, $cursorOffset);
			}

			switch ($this->_fetchMode['Mode']) {
				case SiTech_DB::FETCH_ASSOC:
				case SiTech_DB::FETCH_BOTH:
				case SiTech_DB::FETCH_NUM:
				case SiTech_DB::FETCH_OBJ:
					$row = $this->_fetch($this->_fetchMode['Mode']);
					break;

				case SiTech_DB::FETCH_BOUND:
					$row = $this->_fetch(SiTech_DB::FETCH_BOTH);
					/* TODO: add code to bind to variables */
					$row = true;
					break;

				case SiTech_DB::FETCH_CLASS:
					if (!class_exists($this->_fetchMode['Arg1'])) {
						/* not sure what to do here yet... */
						$row = false;
					} else {
						$row = $this->_fetch(SiTech_DB::FETCH_CLASS, $this->_fetchMode['Arg1'], $this->_fetchMode['Arg2']);
					}
					break;

				case SiTech_DB::FETCH_CLASSTYPE:
					throw new SiTech_DB_Exception('Unsupported fetch mode SiTech_DB::FETCH_CLASSTYPE');
					break;

				case SiTech_DB::FETCH_COLUMN:
					$row = $this->_fetch($this->_fetchMode['Mode'], $this->_fetchMode['Arg1']);
					break;

				case SiTech_DB::FETCH_FUNC:
					$row = call_user_func_array($this->_fetchMode['Arg1'], $this->_fetch(SiTech_DB::FETCH_NUM));
					break;

				case SiTech_DB::FETCH_GROUP:
					$row = $this->_fetch(SiTech_DB::FETCH_BOTH);
					break;

				case SiTech_DB::FETCH_INTO:
					$tmpRow = $this->_fetch(SiTech_DB::FETCH_ASSOC);
					$row = $this->_fetchMode['Arg1'];
					foreach ($tmpRow as $field => $value) {
						$row->$field = $value;
					}
					break;

				case SiTech_DB::FETCH_KEY_PAIR:
					if ($this->columnCount() > 2) {
						throw new SiTech_DB_Exception('SiTech_DB::FETCH_KEY_PAIR fetch mode expects exactly 2 columns in result set.');
					} else {
						$tmpRow = $this->_fetch(SiTech_DB::FETCH_NUM);
						$row = array(
							$tmpRow[0] => $tmpRow[1]
						);
					}
					break;

				case SiTech_DB::FETCH_LAZY:
					throw new SiTech_Exception('Unsupported fetch mode SiTech_DB::FETCH_LAZY');
					break;

				case SiTech_DB::FETCH_NAMED:
					$row = $this->_fetch(SiTech_DB::FETCH_ASSOC);
					break;

				case SiTech_DB::FETCH_SERIALIZE:
					$row = serialize($this->_fetch(SiTech_DB::FETCH_ASSOC));
					break;

				case SiTech_DB::FETCH_UNIQUE:
					/* Not sure what to do here... */
					$row = $this->_fetch(SiTech_DB::FETCH_ASSOC);
					break;

				default:
					throw new SiTech_DB_Exception('Unknown fetch mode %s', array($this->_fetchMode['Mode']));
					break;
			}
		} catch (Exception $e) {
			$this->_fetchMode = $oldFetchMode;
			throw $e;
		}

		if (!empty($oldFetchMode)) {
			$this->_fetchMode = $oldFetchMode;
		}
		
		return($row);
	}

	/**
	 * Return an array of all the rows in the result set.
	 *
	 * @param int $fetchMode Fetch mode to use when fetching all rows.
	 * @param misc $arg1
	 * @param misc $arg2
	 * @return array
	 */
	public function fetchAll($fetchMode=null, $arg1=null, $arg2=null)
	{
		$oldFetchMode = $this->_fetchMode;
		$this->setFetchMode($fetchMode, $arg1, $arg2);
		$array = array();

		try {
			while (($row = $this->fetch()) !== false) {
				if ($this->_fetchMode['Mode'] == SiTech_DB::FETCH_NAMED) {
					$keys = array_keys($row);
					foreach ($keys as $key) {
						if (!isset($array[$key])) {
							$array[$key] = array();
						}

						$array[$key][] = $row[$key];
					}
				} else {
					$array[] = $row;
				}
			}
		} catch (Exception $e) {
			/* handy... cleanup, but still pass the exception */
			$this->_fetchMode = $oldFetchMode;
			throw $e;
		}

		$this->_fetchMode = $oldFetchMode;
		return($array);
	}

	/**
	 * Return a single column from the next row in a result set.
	 *
	 * @param int $columnNumber Column index to return.
	 * @return string
	 */
	public function fetchColumn($columnNumber=0)
	{
		$row = $this->fetch(SiTech_DB::FETCH_COLUMN, $columnNumber);
		return($row);
	}

	/**
	 * Fetch the next row from a result set and return it as an object.
	 *
	 * @param string $className Class to use when creating object.
	 * @param array $constructArgs Array of arguments to pass to the constructor of the new object.
	 * @return mixed
	 */
	public function fetchObject($className=null, array $constructArgs=array())
	{
		$oldFetchMode = $this->_fetchMode;
		$this->setFetchMode(SiTech_DB::FETCH_OBJ, $className, $constructArgs);

		try {
			$row = $this->fetch();
		} catch (Exception $e) {
			$this->_fetchMode = $oldFetchMode;
			throw $e;
		}

		$this->_fetchMode = $oldFetchMode;
		return($row);
	}

	/**
	 * Retreive the specified statement attribute.
	 *
	 * @param int $attribute Attribute to get the value of.
	 * @return mixed Return false if attribute is not set.
	 */
	public function getAttribute($attribute)
	{
		if (isset($this->_attributes[$attribute])) {
			return($this->_attributes[$attribute]);
		} else {
			return(false);
		}
	}

	/**
	 * Set an attribute for the current statement.
	 *
	 * @param int $attribute Attribute to set value to.
	 * @param mixed $value Value to set attribute to.
	 * @return bool
	 * @todo Check and see if $attribute is valid and can be set to $value.
	 */
	public function setAttribute($attribute, $value)
	{
		/* TODO: Check if $attribute is valid and can be set to $value. */
		$this->_attributes[$attribute] = $value;
		return(true);
	}

	/**
	 * Set the default fetch mode for the current statement.
	 *
	 * @param int $mode Fetch mode to set current statement to.
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @return bool
	 * @todo Parse each fetch mode and check arguments.
	 */
	public function setFetchMode($mode, $arg1=null, $arg2=null)
	{
		/* TODO: Parse each fetch mode and check arguments. */
		$this->_fetchMode = array(
			'Mode' => $mode,
			'Arg1' => $arg1,
			'Arg2' => $arg2
		);
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
	 * Parse parameters out of SQL string.
	 *
	 * @param mixed $sql
	 */
	protected function _parseParams($sql)
	{
		$this->_sql = $sql;
		$sqlSplit = preg_split('#(\?|\:[a-z0-9_]+)#i', $sql, null,PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);

		foreach ($sqlSplit as $var) {
			if ($var == '?' || $var[0] == ':') {
				$this->_sqlParams[] = $var;
			}
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
	abstract protected function _bindColumn($column, &$var, $type=null);
	
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
	abstract protected function _bindParam($parameter, &$var, $type, $length, array $driverOptions);
	
	/**
	 * Execute the prepared statement.
	 *
	 * @return bool
	 */
	abstract protected function _execute();
	
	abstract protected function _fetch($mode, $arg1=null, $arg2=null);
	
	/**
	 * Prepare SQL for execution.
	 *
	 * @param string $sql
	 * @return string
	 */
	abstract protected function _prepareSql($sql);
}
?>