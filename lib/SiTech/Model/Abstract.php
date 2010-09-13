<?php
/**
 * Description of SiTech_Model_Abstract
 *
 * @author Eric Gach <eric@php-oop.net>
 */
abstract class SiTech_Model_Abstract
{
	protected static $_db;

	protected $_fields = array();

	protected static $_pk;

	protected static $_table;

	/**
	 * Get a field from the current record.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		/*if (property_exists($this, $name)) {
			return($this->$name);
		} else*/if (isset($this->_fields[$name])) {
			return($this->_fields[$name]);
		} else {
			return(null);
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
		/*if (property_exists($this, $name)) {
			$this->$name = $value;
		} else {*/
			$this->_fields[$name] = $value;
		//}
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
		if (empty(self::$_pk)) self::$_pk = 'Id';
		if (empty(self::$_table)) self::$_table = get_parent_class ();

		if (empty($db) && !is_a(self::$_db, 'PDO')) {
			throw new SiTech_Exception('Cannot get %s::$_db property is not set', array(get_parent_class()));
		} elseif (empty($db)) {
			return(self::$_db);
		} else {
			self::$_db = $db;
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
		$db = self::db();
		$stmnt = $db->prepare('DELETE FROM '.self::$_table.' WHERE '.self::$_pk.' = ?');
		$stmnt->execute(array($this->_vars[self::$_pk]));
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
		$sql = 'SELECT * FROM '.self::$_table;

		if (!empty($where)) {
			$sql .= 'WHERE '.$where;
		}

		$db = self::db();
		$stmnt = $db->query($sql);
		$stmnt->setFetchMode(PDO::FETCH_CLASS, get_parent_class());

		if ($only_one) {
			return($stmnt->fetch());
		} else {
			return($stmnt->fetchAll());
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
		if (empty($this->_fields[self::$_pk])) {
			return($this->_insert());
		} else {
			return($this->_update());
		}
	}

	private function _insert()
	{
		$sql = 'INSERT INTO '.self::$_table.' ';
		$fields = array();
		$values = array();

		foreach ($this->_fields as $f => $v) {
			$fields[] = $f;
			$values[] = $v;
		}

		$sql .= '('.implode(',', $fields).') VALUES('.implode(',', $values).')';
		$db = self::db();
		$stmnt = $db->prepare($sql);
		$stmnt->execute(array($values));
		return($stmnt->rowCount());
	}

	private function _update()
	{
		$sql = 'UPDATE '.self::$_table.' SET ';
		$values = array();

		foreach ($this->_fields as $f => $v) {
			if ($f == self::$_pk) continue; // We don't update the value of the pk
			$sql .= $f.' = ? ';
			$values[] = $v;
		}

		$sql .= 'WHERE '.self::$_pk.' = ?';
		$values[] = $this->_fields[self::$_pk];
		$db = self::db();
		$stmnt = $db->prepare($sql);
		$stmnt->execute($values);
		return((bool)$stmnt->rowCount());
	}
}
