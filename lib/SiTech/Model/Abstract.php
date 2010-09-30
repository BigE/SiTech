<?php
/**
 * I've had vodka, and this is the result. pwn.
 */

/**
 * Description of SiTech_Model_Abstract
 *
 * @author Eric Gach <eric@php-oop.net>
 */
abstract class SiTech_Model_Abstract
{
	/**
	 * This is used to tell the model that it belongs to another model. Through
	 * this you can specify a parent relationship for the model.
	 *
	 * @var array
	 */
	protected $_belongsTo = array();

	/**
	 * PDO storage holder for the database connection.
	 *
	 * @var PDO
	 */
	protected static $db;

	/**
	 * Database holder once the object is created.
	 *
	 * @var PDO
	 */
	protected $_db;

	/**
	 * Errors produced by record validation. After using validate() or save()
	 * that returns false, this should be checked for errors. Once all errors
	 * have been processed, it's reccomended that you clear this array.
	 *
	 * @var array
	 * @see validate
	 */
	public $errors = array();

	/**
	 * Fields stored to the database table.
	 *
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * This is used to tell the model which model to use for specified fields
	 * that can have a one to many or many to many relationship with another
	 * model.
	 *
	 * @var array
	 */
	protected $_hasMany = array();

	/**
	 * This is used to tell the model which model to use for specified fields
	 * that can have a one to one relationship with another model.
	 *
	 * @var array
	 */
	protected $_hasOne = array();

	/**
	 * Primary key for the table. This must be set for the model to be accessed
	 * by any of the methods.
	 *
	 * @var string
	 */
	protected static $_pk;

	/**
	 * Name of table we're using for the model. This must be set for the model to
	 * be accessed by any of the methods.
	 *
	 * @var string
	 */
	protected static $_table;

	public function __construct(PDO $db = null)
	{
		if (empty($db)) {
			$this->_db = self::db();
		} else {
			$this->_db = $db;
		}
	}

	/**
	 * Get a field from the current record.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->_fields[$name]) || isset($this->_hasOne[$name]) || isset($this->_hasMany[$name]) || isset($this->_belongsTo[$name])) {
			$value = (isset($this->_fields[$name]))? $this->_fields[$name] : null;
			
			if ((isset($this->_hasMany[$name]) || isset($this->_hasOne[$name])) && (!is_object($value) || !is_array($value))) {
				// Initalize the class with the name of the variable
				$class = $name;
				$fk = null;
				$one = false;
				// Now check for override settings
				if (isset($this->_hasMany[$name])) {
					if (isset($this->_hasMany[$name]['class'])) {
						$class = $this->_hasMany[$name]['class'];
					}

					if (isset($this->_hasMany[$name]['foreignKey'])) {
						$fk = $this->_hasMany[$name]['foreignKey'].'='.$this->_fields[self::pk()];
					}
				} elseif (isset($this->_hasOne[$name])) {
					if (isset($this->_hasOne[$name]['class'])) {
						$class = $this->_hasOne[$name]['class'];
					}

					if (isset($this->_hasOne[$name]['foreignKey'])) {
						$fk = $this->_hasOne[$name]['foreignKey'].'='.$this->_fields[self::pk()];
					}

					$one = true;
				} elseif (isset($this->_belongsTo[$name])) {
					if (isset($this->_belongsTo[$name]['class'])) {
						$class = $this->_belongsTo[$name]['class'];
					}

					if (isset($this->_belongsTo[$name]['foreignKey'])) {
						$fk = $this->_belongsTo[$name]['foreignKey'].'='.$this->_fields[self::pk()];
					}

					$one = true;
				}

				SiTech_Loader::loadModel($class);
				$class .= 'Model';
				$value = $class::get($fk, $one);
				$this->_fields[$name] = $value;
			}

			return($value);
		} else {
			return(null);
		}
	}

	public function __isset($name)
	{
		if (isset($this->_fields[$name])) {
			return(true);
		} else {
			return(false);
		}
	}

	/**
	 * Set the value of a field for the record.
	 *
	 * @param string $name Field name to set
	 * @param string $value Value to set the field to
	 */
	public function __set($name, $value)
	{
		$this->_fields[$name] = $value;
	}

	/**
	 * Get or set the database connection for the model to use. If no connection
	 * is set and you call this method without specifying a connection, an
	 * exception will be thrown.
	 *
	 * @param PDO $db
	 * @return PDO
	 * @throws SiTech_Exception
	 */
	public static function db(PDO $db = null)
	{
		/**
		 * This is kinda like an init class since our internal methods use it,
		 * so lets do some basic checks.
		 */
		if (empty(static::$_table)) self::$_table = get_parent_class();

		if (empty($db) && !is_a(static::$db, 'PDO')) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('The %s::$_db property is not set. Please use %s::db() to set the PDO connection.', array(get_parent_class(), get_parent_class()));
		} elseif (empty($db)) {
			return(static::$db);
		} else {
			static::$db = $db;
		}
	}

	/**
	 * Once the model is a record, this is the delete method. It will delete
	 * the current record based on the primary key. It will return true if the
	 * record is successfully deleted, or false if it is not.
	 *
	 * @return bool
	 */
	public function delete()
	{
		$pk = self::pk();

		$stmnt = $this->_db->prepare('DELETE FROM '.static::$_table.' WHERE '.$pk.' = ?');
		$stmnt->execute(array($this->_fields[$pk]));
		return((bool)$stmnt->rowCount());
	}

	/**
	 * Get records from the table tied to the model.
	 *
	 * @param string $where WHERE clause of the SQL query.
	 * @param bool $only_one Set to true to only return a single record
	 * @return mixed
	 */
	public static function get($where = '1', $only_one = false)
	{
		$sql = 'SELECT * FROM '.static::$_table;

		if (!empty($where)) {
			$sql .= ' WHERE '.$where;
		}

		$stmnt = static::db()->query($sql);
		$stmnt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

		if ($only_one) {
			return($stmnt->fetch());
		} else {
			return($stmnt->fetchAll());
		}
	}

	public static function pk($pk = null)
	{
		if (empty($pk) && empty(static::$_pk)) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('%s::$_pk is not set. Please use %s::pk() to set the primary key field.', array(get_parent_class(), get_parent_class()));
		} elseif (!empty($pk)) {
			static::$_pk = $pk;
		} else {
			return(static::$_pk);
		}
	}

	/**
	 * Save the record to the database. If the primary key is not set to a value
	 * then it will use an INSERT statement, otherwise it will use UPDATE based
	 * on the primary key.
	 *
	 * @return bool
	 */
	public function save()
	{
		if (!$this->validate()) {
			return(false);
		}

		if (empty($this->_fields[self::pk()])) {
			return($this->_insert());
		} else {
			return($this->_update());
		}
	}

	/**
	 * Validate data coming in to record before saving. Defaults to return true,
	 * so if any validation needs to be done, it should be overridden in the
	 * parent class.
	 *
	 * @return bool
	 */
	public function validate()
	{
		return(true);
	}

	/**
	 * Insert the record into the database. This simply builds and executes an
	 * INSERT query based on the fields specified. If there were rows affected
	 * by the insert, this returns true, otherwise it returns false.
	 *
	 * @return bool
	 */
	private function _insert()
	{
		$pk = self::pk();
		$sql = 'INSERT INTO '.static::$_table.' ';
		$fields = array();
		$values = array();

		foreach ($this->_fields as $f => $v) {
			if ($f == $pk) continue;
			$fields[] = $f;
			$values[] = $v;
		}

		$sql .= '('.implode(',', $fields).') VALUES('.implode(',', $values).')';
		$stmnt = $this->_db->prepare($sql);
		$stmnt->execute($values);
		return($stmnt->rowCount());
	}

	/**
	 * Update the existing record in the database based on the primary key that
	 * is specified. If the query returns rows affected, this will return true
	 * otherwise it will return false if no rows are affected.
	 *
	 * @return bool
	 */
	private function _update()
	{
		$pk = self::pk();
		$sql = 'UPDATE '.static::$_table.' SET ';
		$fields = array();
		$values = array();

		foreach ($this->_fields as $f => $v) {
			if ($f == $pk) continue; // We don't update the value of the pk
			$fields[] = $f.' = ?';
			$values[] = $v;
		}

		$sql .= implode(',', $fields);
		$sql .= ' WHERE '.$pk.' = ?';
		$values[] = $this->_fields[$pk];
		$stmnt = $this->_db->prepare($sql);
		$stmnt->execute($values);
		return(($stmnt->rowCount() === false)? false : true);
	}
}
