<?php
/**
 * SiTech Database Statement base.
 *
 * @package SiTech_DB
 */

/**
 * Require SiTech base.
 */
require_once('SiTech.php');
SiTech::loadInterface('SiTech_DB_Statement_Interface');

/**
 * Base statement functionality. Anything that isn't database specific is defined here.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Statement_Base
 * @package SiTech_DB
 */
abstract class SiTech_DB_Statement_Base implements SiTech_DB_Statement_Interface
{
	/**
	 * Returns a standard object for each row with the columns as object parameters.
	 */
	const FETCH_LAZY = 1;

	/**
	 * An array will be returned for each row containting only the named indexes for each
	 * column in the row.
	 */
	const FETCH_ASSOC = 2;

	/**
	 * An array will be returned for each column containing an index for each row from
	 * the rowset.
	 */
	const FETCH_NAMED = 3;

	/**
	 * An array will be returned for each row containing only the indexes for each column
	 * in the row.
	 */
	const FETCH_NUM = 4;

	/**
	 * An array will be returned for each row containing a named and indexes for each
	 * column in the row.
	 */
	const FETCH_BOTH = 5;

	/**
	 * Returns each row as the object specified and maps all values to parameters within
	 * the object.
	 */
	const FETCH_OBJ = 6;

	/**
	 * Fetches the columns from the row into the variables that were used with
	 * self::bindColumn()
	 */
	const FETCH_BOUND = 7;

	/**
	 * Only return a single specified column from the row.
	 */
	const FETCH_COLUMN = 8;

	/**
	 * Creates a new object of the specified class and maps all values to parameters within
	 * the class.
	 */
	const FETCH_CLASS = 9;

	/**
	 * Fetches the row into an existing object of the class and updates the values.
	 */
	const FETCH_INTO = 10;

	/**
	 * Use a function as a callback when the row is fetched.
	 */
	const FETCH_FUNC = 11;

	protected $_args = array();

	protected $_columns = array();

	protected $_params = array();

	protected $_result;

	protected $_sql;

	public function __construct($sql, $conn, $args=array())
	{
		$this->_args = $args;
		$this->_conn = $conn;
		$this->_sql = $sql;
	}

	/**
	 * Bind a column in the query to a specified variable.
	 *
	 * @param int $column Column number
	 * @param mixed $var Variable to bind column to
	 * @param int $type Constant that specifies the type of the variable
	 * @return bool
	 */
	public function bindColumn($column, &$var, $type=null)
	{
		if (!isset($this->_column[$column])) {
			$this->_column[$column] = array();
		}

		$this->_column[$column]['type'] = $type;
		$this->_column[$column]['value'] &= $var;
		return(true);
	}

	/**
	 * Bind a parameter name to a specific variable.
	 *
	 * @param mixed $param Param number or name to bind variable to
	 * @param mixed $var Variable to bind parameter to
	 * @param int $type Constant that specifies parameter type
	 * @param int $length Maximum allowed length of parameter
	 * @param array $options Options for parameter
	 * @return bool
	 */
	public function bindParam($param, &$var, $type=null, $length=null, $options=array())
	{
		if (!isset($this->_params[$param])) {
			$this->_params[$param] = array();
		}

		$this->_params[$param]['length'] = $length;
		$this->_params[$param]['type'] = $type;
		$this->_params[$param]['value'] &= $var;
		return(true);
	}

	/**
	 * Bind a value to the specified parameter.
	 *
	 * @param mixed $param Param number or name to bind variable to
	 * @param mixed $val Value for parameter
	 * @param int $type Constant specifying values type
	 * @return bool
	 */
	public function bindValue($param, $val, $type=null)
	{
		if (!isset($this->_params[$param])) {
			$this->_params[$param] = array();
		}

		$this->_params[$param]['type'] = $type;
		$this->_params[$param]['value'] = $val;
	}

	/**
	 * Fetch a single row from the database and return the results formatted by the
	 * specified fetch mode.
	 *
	 * @param int $fetchMode Constant of the fetch mode
	 * @param unknown_type $cursor ?
	 * @param int $offset Numeric offset of row to grab
	 * @return mixed
	 */
	public function fetch($fetchMode=null, $cursor=null, $offset=null)
	{
		$fetch = is_null($fetchMode)? $this->_fetchMode : array('mode' => $fetchMode);

		if (($row = $this->_fetch($offset)) === false) {
			return(false);
		}

		$columns = array_keys($row);
		$values = array_values($row);

		switch ($fetch['mode'])
		{
			case self::FETCH_LAZY:
				SiTech::loadClass('SiTech_DB_Statement_Exception');
				throw new SiTech_DB_Statement_Exception('%s::FETCH_LAZY is not implemented yet', __CLASS__);
				break;

			case self::FETCH_ASSOC:
				return(array_combine($columns, $values));
				break;

			case self::FETCH_NAMED:
				SiTech::loadClass('SiTech_DB_Statement_Exception');
				throw new SiTech_DB_Statement_Exception('%s::FETCH_NAMED is not implemented yet', __CLASS__);
				break;

			case self::FETCH_NUM:
				return($values);
				break;

			case self::FETCH_BOTH:
				return(array_merge(array_combine($columns, $values), $values));
				break;

			case self::FETCH_OBJ:
				return((object)array_combine($columns, $values));
				break;

			case self::FETCH_BOUND:
				SiTech::loadClass('SiTech_DB_Statement_Exception');
				throw new SiTech_DB_Statement_Exception('%s::FETCH_BOUND is not implemented yet', __CLASS__);
				break;

			case self::FETCH_COLUMN:
				if (!isset($fetch['column'])) {
					SiTech::loadClass('SiTech_DB_Statement_Exception');
					throw new SiTech_DB_Statement_Exception('%s::FETCH_COLUMN must be set through %s::setFetchMode and cannot be directly set through %s::%s', __CLASS__, __CLASS__, __CLASS__, __FUNCTION__);
				}

				if (!isset($row[$fetch['column']])) {
					/* I'm not sure if I want to throw an exception here yet... */
					return(null);
				}

				return($row[$fetch['column']]);
				break;

			case self::FETCH_CLASS:
				if (!isset($fetch['class'])) {
					SiTech::loadClass('SiTech_DB_Statement_Exception');
					throw new SiTech_DB_Statement_Exception('%s::FETCH_CLASS is missing class to fetch into. Please call %s::setFetchMode with proper arguments.', __CLASS__, __CLASS__);
				}

				$obj = new $fetch['class']();
				foreach ($columns as $key => $column) {
					if (!is_numeric($column)) {
						$obj->$column = $values[$key];
					}
				}
				return($obj);
				break;

			case self::FETCH_INTO:
				SiTech::loadClass('SiTech_DB_Statement_Exception');
				throw new SiTech_DB_Statement_Exception('%s::FETCH_INTO is not implemented yet', __CLASS__);
				break;

			case self::FETCH_FUNC:
				if (!isset($fetch['callback'])) {
					SiTech::loadClass('SiTech_DB_Statement_Exception');
					throw new SiTech_DB_Statement_Exception('%s::FETCH_FUNC must be set through %s::setFetchMode and cannot be directly set through %s::%s', __CLASS__, __CLASS__, __CLASS__, __FUNCTION__);
				}

				if (!function_exists($fetch['callback'])) {
					SiTech::loadClass('SiTech_DB_Statement_Exception');
					throw new SiTech_DB_Statement_Exception('The callback function "%s" was not found - Unable to process row', $fetch['callback']);
				}

				return(call_user_func_array($fetch['callback'], array($columns, $values)));
				break;

			default:
				SiTech::loadClass('SiTech_DB_Statement_Exception');
				throw new SiTech_DB_Statement_Exception('Unsupported fetch type specified');
				break;
		}
	}

	/**
	 * Return all rows in the result in one big array.
	 *
	 * @param const $fetchStyle Fetch style for grabbing each row
	 * @param int $column Column number in rowset to start grabbing at
	 * @return array
	 */
	public function fetchAll($fetchStyle=null, $column=0)
	{
		$array = array();

		while ($row = $this->fetch($fetchStyle, null, $column++)) {
			$array[] = $row;
		}

		return($array);
	}

	/**
	 * Return a single column from the next row of a result. Returns false if no more rows
	 * are available.
	 *
	 * @param int $column Column index to grab
	 * @return string
	 */
	public function fetchColumn($column=0)
	{
		$row = $this->fetch(self::FETCH_COLUMN, $column);
		return($row);
	}

	/**
	 * Fetch the next row in the result and return it in an object.
	 *
	 * @param string $class Class name to put row in - Default stdclass
	 * @param array $args Array of arguments to pass to the constructor
	 * @return mixed
	 */
	public function fetchObject($class=null, $args=array())
	{
		if (is_null($class)) {
			$class = 'stdclass';
		}

		$fetchMode = $this->_fetchMode;
		$this->setFetchMode(self::FETCH_CLASS, $class, $args);
		$row = $this->fetch();
		$this->_fetchMode = $fetchMode;

		return($row);
	}

	/**
	 * NOT IMPLEMENTED: Get an attribute set over the current statement.
	 *
	 * @param int $attr Attribute to retrive the value for
	 * @return mixed
	 */
	public function getAttribute($attr)
	{
		SiTech::loadClass('SiTech_DB_Statement_Exception');
		throw new SiTech_DB_Statement_Exception('%s::%s is not yet implemented', __CLASS__, __FUNCTION__);
	}

	/**
	 * NOT IMPLEMENTED: Advances to the next rowset returned by the query. This is
	 * basically only used in databases that accept multiple queries or use stored
	 * procedures that return multiple rowsets.
	 *
	 * @return bool
	 */
	public function nextRowset()
	{
		SiTech::loadClass('SiTech_DB_Statement_Exception');
		throw new SiTech_DB_Statement_Exception('%s::%s is not yet implemented', __CLASS__, __FUNCTION__);
	}

	/**
	 * Set an attribute for the statement.
	 *
	 * @param int $attr Attribute to set value for
	 * @param mixed $value Value to set attribute to
	 * @return bool
	 */
	public function setAttribute($attr, $value)
	{
		SiTech::loadClass('SiTech_DB_Statement_Exception');
		throw new SiTech_DB_Statement_Exception('%s::%s is not yet implemented', __CLASS__, __FUNCTION__);
	}

	/**
	 * Set the default fetch mode for this statement.
	 *
	 * @param int $mode Fetch mode constant
	 * @param mixed $arg1 First argument for specified fetch mode
	 * @param mixed $arg2 Second argument for specified fetch mode
	 * @return bool
	 */
	public function setFetchMode($mode, $arg1=null, $arg2=null)
	{
		switch ($mode)
		{
			case self::FETCH_LAZY:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_ASSOC:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_NAMED:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_NUM:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_BOTH:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_OBJ:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_BOUND:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_COLUMN:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_CLASS:
				$this->_fetchMode['mode'] = $mode;
				$this->_fetchMode['class'] = $arg1;
				break;

			case self::FETCH_INTO:
				$this->_fetchMode['mode'] = $mode;
				break;

			case self::FETCH_FUNC:
				$this->_fetchMode['mode'] = $mode;

				if (is_null($arg1)) {
					SiTech::loadClass('SiTech_DB_Statement_Exception');
					throw new SiTech_DB_Statement_Exception('Argument 2 of %s::%s is empty, expecting callback function', __CLASS__, __FUNCTION__);
				}

				if (!function_exists($arg1)) {
					/* It might not be defined yet, so no exception - we check again in fetch() */
				}

				$this->_fetchMode['callback'] = $arg1;
				break;

			default:
				/* exception */
				break;
		}

		return(true);
	}

	protected function _prepareSql()
	{
		/* easy enough for now.. sprintf! */
		if (!empty($this->_params)) {
			foreach ($this->_params as $field => $param) {
				if (!ctype_digit($param)) {
					$param = "'$param'";					
				}

				$this->_sql = str_replace($field, $param, $this->_sql);
			}
		}

		if (!empty($this->_args)) {
			$this->_sql = vsprintf($this->_sql, $this->_args);
		}
	}

	abstract protected function _fetch($offset);
}
?>
