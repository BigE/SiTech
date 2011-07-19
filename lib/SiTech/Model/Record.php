<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace SiTech\Model;

/**
 * @see SiTech\Model\Base
 */
require_once('SiTech/Model/Base.php');

/**
 * Description of Record
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class Record extends Base
{
	/**
	 * This is used to tell the model that it belongs to another model. Through
	 * this you can specify a parent relationship for the model.
	 *
	 * @var array
	 */
	protected $_belongsTo = array();

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
	 * Get a field from the current record.
	 *
	 * @param string $name Name of the field to get
	 * @return mixed Returns null if no value is set.
	 */
	public function __get($name)
	{
		if (isset($this->_fields[$name]) || isset($this->_hasOne[$name]) || isset($this->_hasMany[$name]) || isset($this->_belongsTo[$name])) {
			$value = (isset($this->_fields[$name]))? $this->_fields[$name] : null;

			if ((isset($this->_hasMany[$name]) || isset($this->_hasOne[$name]) || isset($this->_belongsTo[$name])) && (!is_object($value) && !is_array($value))) {
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
						$fk = $this->_hasMany[$name]['foreignKey'].'='.$this->_fields[static::pk()];
					}
				} elseif (isset($this->_hasOne[$name])) {
					if (isset($this->_hasOne[$name]['class'])) {
						$class = $this->_hasOne[$name]['class'];
					}

					if (isset($this->_hasOne[$name]['foreignKey'])) {
						$fk = $this->_hasOne[$name]['foreignKey'].'='.$this->_fields[static::pk()];
					}

					$one = true;
				} elseif (isset($this->_belongsTo[$name])) {
					if (isset($this->_belongsTo[$name]['class'])) {
						$class = $this->_belongsTo[$name]['class'];
					}

					if (isset($this->_belongsTo[$name]['foreignKey'])) {
						$fk = $this->_belongsTo[$name]['foreignKey'].'='.$this->_fields[$name];
					}

					$one = true;
				}

				require_once('SiTech/Loader.php');
				\SiTech\Loader::loadModel($class);
				$class .= 'Model';
				$value = $class::find($fk, $one);
				$this->_fields[$name] = $value;
			}

			return($value);
		} else {
			return(null);
		}
	}

	/**
	 * Magic method to check if a field has a value set or not.
	 *
	 * @param type $name
	 * @return bool Returns true if set and false if not
	 */
	public function __isset($name)
	{
		return((isset($this->_fields[$name]))? true : false);
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

	public function __unset($name)
	{
		unset($this->_fields[$name]);
	}

	/**
	 * Get a total of rows based on the chriteria passed in.
	 *
	 * @param string $where Chriteria to use when counting rows.
	 * @return int Total rows that match chriteria
	 */
	public static function count($where = null)
	{
		$sql = 'SELECT COUNT('.static::pk().') FROM '.static::$_table;

		if (!empty($where)) {
			if (\is_int($where)) {
				$sql .= ' WHERE '.static::pk().' = '.$where;
			} else {
				$sql .= ' WHERE '.$where;
			}
		}

		$stmnt = static::db()->query($sql);
		return((int)$stmnt->fetchColumn());
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
		$pk = static::pk();

		$stmnt = $this->_db->prepare('DELETE FROM '.static::$_table.' WHERE '.$pk.' = ?');
		$stmnt->execute(array($this->_fields[$pk]));
		return((bool)$stmnt->rowCount());
	}

	/**
	 * Get records from the table tied to the model. If $only_one is set to false
	 * the model will automatically initate a collection class and return that
	 * for all the results that come from the query.
	 *
	 * @param string $where WHERE clause of the SQL query.
	 * @param bool $only_one When set to false, it will use the Collection class
	 * @return mixed
	 */
	public static function find($where = null, $only_one = true)
	{
		$return = false;

		if ($only_one === true) {
			$sql = 'SELECT * FROM '.static::$_table;
			$args = array();

			if (!empty($where)) {
				if (\is_int($where)) {
					$sql .= ' WHERE '.static::pk().' = ?';
					$args = array($where);
				} else {
					$sql .= static::_where($where);
					$args = static::_whereArgs($where);
				}
			}

			// Use LIMIT in our query if no LIMIT is already specified... we
			// only want one record anyway.
			if (!preg_match('#LIMIT [0-9]+#', $sql)) $sql .= ' LIMIT 1';

			$stmnt = static::db()->prepare($sql);
			$stmnt->execute($args);
			$stmnt->setFetchMode(\PDO::FETCH_CLASS, \get_called_class());
			$return = $stmnt->fetch();
			$stmnt->closeCursor();
		} else {
			require_once('SiTech/Model/Collection.php');
			$collection = new Collection($where, array(Collection\ATTR_MODEL => get_called_class()));
			$return = $collection;
		}

		return($return);
	}

	/**
	 * This runs a REPLACE INTO query on the database server. Some SQL servers
	 * do not support this, so please be sure your SQL server does.
	 */
	public function replace()
	{
	}

	/**
	 * Save the record to the database. If the primary key is not set to a value
	 * then it will use an INSERT statement, otherwise it will use UPDATE based
	 * on the primary key.
	 *
	 * @return bool
	 */
	public function save($duplicateKeyUpdate = false)
	{
		if (!$this->validate()) {
			return(false);
		}

		if (empty($this->_fields[static::pk()]) || $duplicateKeyUpdate === true) {
			return($this->_insert($duplicateKeyUpdate));
		} else {
			return($this->_update());
		}
	}

	/**
	 * Take the data from the current model and encode it into a JSON string.
	 *
	 * @return string
	 */
	public function toJson(array $fields = array())
	{
		if (!empty($fields)) {
			$array = array();
			foreach ($fields as $field) {
				$array[$field] = $this->_fields[$field];
			}

			return(\json_encode($array));
		} else {
			return(\json_encode($this->_fields));
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
	private function _insert($duplicateKeyUpdate)
	{
		$pk = static::pk();
		$sql = 'INSERT INTO '.static::$_table.' ';
		$fields = array();
		$values = array();

		foreach ($this->_fields as $f => $v) {
			if ($f == $pk) continue;
			$fields[] = $f;
			$values[$f] = ($v instanceof \SiTech\Model\Base)? $v->{$v::pk()} : $v;
		}

		$sql .= '('.\implode(',', $fields).') VALUES(:'.\implode(',:', $fields).')';
		if ($duplicateKeyUpdate) {
			// Unique duplicate key maybe?
			$sql .= ' ON DUPLICATE KEY UPDATE ';
			//$sql .= static::pk().' = LAST_INSERT_ID('.static::pk().')';
			foreach ($fields as $field) {
				$sql .= ' '.$field.' = :'.$field.',';
			}

			// Get rid of the trailing character
			$sql = substr($sql, 0, -1);
		}
		$stmnt = $this->_db->prepare($sql);
		if ($stmnt->execute($values)) {
			// Assign the PK once the row is inserted
			$this->_fields[$pk] = $this->_db->lastInsertId();
		}
		return((bool)$stmnt->rowCount());
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
		$pk = static::pk();
		$sql = 'UPDATE '.static::$_table.' SET ';
		$fields = array();
		$values = array();

		foreach ($this->_fields as $f => $v) {
			if ($f == $pk) continue; // We don't update the value of the pk
			$fields[] = $f.' = ?';
			$values[] = ($v instanceof \SiTech\Model\Base)? $v->{$v::pk()} : $v;
		}

		$sql .= \implode(',', $fields);
		$sql .= ' WHERE '.$pk.' = ?';
		$values[] = $this->_fields[$pk];
		$stmnt = $this->_db->prepare($sql);
		$stmnt->execute($values);
		return(($stmnt->rowCount() === false)? false : true);
	}
}
