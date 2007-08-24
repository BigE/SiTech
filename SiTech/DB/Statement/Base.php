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
class SiTech_DB_Statement_Base implements SiTech_DB_Statement_Interface
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
	 * SQL string command.
	 *
	 * @var string
	 */
	protected $_sql;

	/**
	 * Class constructor
	 *
	 * @param mixed $sql String or SiTech_DB_Select object.
	 * @param mixed $conn Connection resource.
	 */
	public function __construct($sql, $conn, $driverOptions=array())
	{
		$this->_sql = $this->_prepareSql($sql);
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
			'variable' => $var,
			'type' => $type
		);

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
	public function bindParam($parameter, &$var, $type=SiTech_DB::PARAM_STR, $length=null, array $driverOptions=array())
	{
		$this->_boundParams[$param] = array(
			'value' => $var,
			'type' => $type,
			'length' => $length,
			'driverOptions' => $driverOptions
		);

		return(true);
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
		$this->_boundParams[$param] = array(
			'value' => $value,
			'type' => $type,
			'length' => null,
			'driverOptions' => array()
		);

		return(true);
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
		$oldFetchMode = $this->_fetchMode;
		try {
			$this->setFetchMode($fetchMode);

			switch ($this->_fetchMode['mode']) {
				case SiTech_DB::FETCH_ASSOC:
				case SiTech_DB::FETCH_BOTH:
				case SiTech_DB::FETCH_NUM:
				case SiTech_DB::FETCH_OBJ:
					$row = $this->_fetch($this->_fetchMode['mode']);
					break;

				case SiTech_DB::FETCH_BOUND:
					$row = $this->_fetch(SiTech_DB::FETCH_BOTH);
					/* TODO: add code to bind to variables */
					$row = true;
					break;

				case SiTech_DB::FETCH_CLASS:
					if (!class_exists($this->_fetchMode['arg1'])) {
						/* not sure what to do here yet... */
						$row = false;
					} else {
						$row = $this->_fetch(SiTech_DB::FETCH_CLASS, $arg1, $arg2);
					}
					break;

				case SiTech_DB::FETCH_CLASSTYPE:
					throw new SiTech_DB_Exception('Unsupported fetch mode SiTech_DB::FETCH_CLASSTYPE');
					break;

				case SiTech_DB::FETCH_COLUMN:
					$row = $this->_fetch($this->_fetchMode['mode'], $this->_fetchMode['arg1']);
					break;

				case SiTech_DB::FETCH_FUNC:
					$row = call_user_func_array($this->_fetchMode['mode'], $this->_fetch(SiTech_DB::FETCH_NUM));
					break;

				case SiTech_DB::FETCH_GROUP:
					$row = $this->_fetch(SiTech_DB::FETCH_BOTH);
					break;

				case SiTech_DB::FETCH_INTO:
					$tmpRow = $this->_fetch(SiTech_DB::FETCH_ASSOC);
					$row = $this->_fetchMode['arg1'];
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
					throw new SiTech_DB_Exception('Unknown fetch mode %s', array($this->_fetchMode['mode']));
					break;
			}
		} catch (Exception $e) {
			$this->_fetchMode = $oldFetchMode;
			throw $e;
		}

		$this->_fetchMode = $oldFetchMode;
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
			while ($row = $this->fetch()) {
				if ($this->_fetchMode['mode'] == SiTech_DB::FETCH_NAMED) {
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
			'mode' => $mode,
			'arg1' => $arg1,
			'arg2' => $arg2
		);
		return(true);
	}

	/**
	 * Prepare SQL string/object to get it ready for execution.
	 *
	 * @param mixed $sql
	 * @return string
	 * @todo Complete parsing of SQL.
	 */
	protected function _preapareSql($sql)
	{
		/* TODO: Complete parsing of SQL. */
		return((string)$sql);
	}

	protected function _fetch($mode, $arg1=null, $arg2=null);
}
?>