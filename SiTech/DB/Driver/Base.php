<?php
/**
 * Base file for SiTech_DB_Driver_*
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * @see SiTech
 */
require_once('SiTech.php');

/**
 * @see SiTech_DB
 */
SiTech::loadClass('SiTech_DB');

/**
 * @see SiTech_DB_Backend_Interface
 */
SiTech::loadInterface('SiTech_DB_Backend_Interface');

/**
 * Base class for SiTech_DB_Driver_* All database backend classes
 * must extend this abstract class.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Driver_Base
 * @package SiTech_DB
 */
abstract class SiTech_DB_Driver_Base implements SiTech_DB_Driver_Interface
{
	protected $_attributes = array(
		SiTech_DB::ATTR_ERRMODE => SiTech_DB::ERRMODE_EXCEPTION
	);

	protected $_config = array();

	protected $_conn;

	public function __construct(array $config)
	{
		/* TODO: Validate $config */
		$this->_config = $config;
	}

	/**
	 * Delete rows from a table.
	 *
	 * @param string $table Table name of where to delete rows from.
	 * @param array $where Array of criteria.
	 * @return int Returns number of rows deleted.
	 * @todo Write WHERE clause
	 */
	public function delete($table, array $where=array())
	{
		$sql = 'DELETE FROM ';
		$sql .= $this->quote($table, SiTech_DB::PARAM_TABLE);
		/* TODO: Formulate WHERE clause */
		return($this->exec($sql));
	}

	/**
	 * Execute a SQL statement on the server.
	 *
	 * @param string $sql SQL to execute on the server.
	 * @param array $params Parameters to bind to the SQL statement.
	 * @return int Number of rows returned by the query.
	 */
	public function exec($sql, array $params=array())
	{
		$stmnt = $this->prepare($sql);
		$stmnt->execute($params);
		return($stmnt->rowCount());
	}

	/**
	 * Retreive an attribute set for the server.
	 *
	 * @param int $attribute SiTech_DB::PARAM_* constant of attribute to get.
	 * @return mixed Attribute's value
	 */
	public function getAttribute($attribute)
	{
		if (isset($this->_attribute[$attribute])) {
			return($this->_attribute[$attribute]);
		} else {
			return(null);
		}
	}

	/**
	 * Get the current fetch mode.
	 *
	 * @return int SiTech_DB::FETCH_* mode currently set.
	 */
	public function getFetchMode()
	{
		return($this->_fetch['Mode']);
	}

	/**
	 * Insert a new row into the database.
	 *
	 * @param string $table Table to insert rows into.
	 * @param array $values Field=>Value style array of values.
	 * @return int Number of rows inserted.
	 */
	public function insert($table, array $values)
	{
		$fields = array_keys($values);

		foreach($fields as $key => $field) {
			$fields[$key] = $this->quote($field, SiTech_DB::PARAM_TABLE);
		}

		$values = array_values($values);

		foreach ($values as $key => $value) {
			$values[$key] = $this->quote($value);
		}

		$sql = 'INSERT INTO ';
		$sql .= $this->quote($table);
		$sql .=' (';
		$sql .= implode(',', $fields);
		$sql .= ') VALUES(';
		$sql .= implode(',', $values);
		$sql .= ')';
		
		return($this->exec($sql));
	}

	/**
	 * Execute a query on the database server.
	 *
	 * @param string $sql
	 * @param int $fetchMethod SiTech_DB::FETCH_* constant
	 * @param mixed $misc1
	 * @param mixed $misc2
	 */
	public function query($sql, $fetchMethod=null, $misc1=null, $misc2=null)
	{
		$stmnt = $this->prepare($sql);
		if (!empty($fetchMethod)) {
			$stmnt->setFetchMode($fetchMethod, $misc1, $misc2);
		}
		$stmnt->execute();
		return($stmnt);
	}

	/**
	 * Return a select object to perform a select query on the database.
	 *
	 * @return SiTech_DB_Select
	 */
	public function select()
	{
		return(new SiTech_DB_Select());
	}

		/**
	 * Set a value to an attribute for the server.
	 *
	 * @param int $attribute SiTech_DB::PARAM_*
	 * @param mixed $value Value to set attribute to.
	 */
	public function setAttribute($attribute, $value)
	{
		$this->_attributes[$attribute] = $value;
	}

	/**
	 * Set the current fetch mode and arguments for it.
	 *
	 * @param int $mode SiTech_DB::FETCH_* constant.
	 * @param mixed $arg1
	 * @param mixed $arg2
	 */
	public function setFetchMode($mode, $arg1, $arg2)
	{
		$this->_fetch['Mode'] = $mode;
		$this->_fetch['Arg1'] = $arg1;
		$this->_fetch['Arg2'] = $arg2;
	}

	/**
	 * Perform an update query to the database.
	 *
	 * @param string $table
	 * @param array $values Field=>Value array of values.
	 * @param array $where
	 * @return int Returns false on failure.
	 * @todo Implement WHERE clause
	 */
	public function update($table, array $values, array $where=array())
	{
		$sql = 'UPDATE ';
		$sql .= $this->quote($table, SiTech_DB::PARAM_TABLE);
		$sql .= ' SET ';
		foreach($values as $field => $value) {
			$values[$field] = $this->quote($field, SiTech_DB::PARAM_FIELD).'='.$this->quote($value);
		}
		$sql .= implode(',', $values);
		/* TODO: Implement WHERE clause */
		return($this->exec($sql));
	}

	abstract protected function _connect();
	abstract protected function _disconnect();
}
?>