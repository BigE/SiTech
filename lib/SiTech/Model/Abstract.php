<?php
/**
 * SiTech/Model/Abstract.php
 *
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
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2010
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Model
 * @todo Finish documentation and fix any remaining bugs.
 * @version $Id$
 */

/**
 * I've had vodka, and this is the result. pwn.
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
	 * This contains all of the fields that have changed in the model. After the
	 * save method is complete, it will clear this array and start fresh.
	 *
	 * @var array
	 */
	protected $_modified = array();

	/**
	 * This keeps track of whether the primary key has been reset
	 *
	 * @var boolean
	 */
	protected $_pk_reset = false;

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
			$this->_db = static::db();
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
		if (isset($this->_fields[$name]) || isset($this->_hasOne[$name]) || isset($this->_hasMany[$name]) || isset($this->_belongsTo[$name]) || isset($this->_modified[$name])) {
			$value = (isset($this->_modified[$name]))? $this->_modified[$name] : ((isset($this->_fields[$name]))? $this->_fields[$name] : null);

			if ((isset($this->_hasMany[$name]) || isset($this->_hasOne[$name]) || isset($this->_belongsTo[$name])) && (!is_object($value) && !is_array($value) && (!isset($this->_fields[$name]) || !empty($value)))) {
				// Initalize the class with the name of the variable
				$class = $name;
				$fk = null;
				// If this is set to true, only one record will be returned.
				$one = false;
				// If ths is set to false, we will not try to load the model from here.
				$autoload = true;
				// Now check for override settings
				if (isset($this->_hasMany[$name])) {
					if (isset($this->_hasMany[$name]['class'])) {
						$class = $this->_hasMany[$name]['class'];
					}

					if (isset($this->_hasMany[$name]['foreignKey'])) {
						$fk = $this->_hasMany[$name]['foreignKey'].'='.$this->_fields[static::pk()];
					}

					if (isset($this->_hasMany[$name]['autoload'])) {
						$autoload = (bool)$this->_hasMany[$name]['autoload'];
					}
				} elseif (isset($this->_hasOne[$name])) {
					if (isset($this->_hasOne[$name]['class'])) {
						$class = $this->_hasOne[$name]['class'];
					}

					if (isset($this->_hasOne[$name]['foreignKey'])) {
						$fk = $this->_hasOne[$name]['foreignKey'].'='.$this->_fields[static::pk()];
					}

					if (isset($this->_hasOne[$name]['autoload'])) {
						$autoload = (bool)$this->_hasOne[$name]['autoload'];
					}

					$one = true;
				} elseif (isset($this->_belongsTo[$name])) {
					if (isset($this->_belongsTo[$name]['class'])) {
						$class = $this->_belongsTo[$name]['class'];
					}

					if (isset($this->_belongsTo[$name]['foreignKey'])) {
						$fk = $this->_belongsTo[$name]['foreignKey'].'='.$value;
					}

					if (isset($this->_belongsTo[$name]['autoload'])) {
						$autoload = (bool)$this->_belongsTo[$name]['autoload'];
					}

					$one = true;
				}

				if ($autoload) {
					SiTech_Loader::loadModel($class);
					$class .= 'Model';
				}

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
		if (!isset($this->_fields[$name]) && (isset($this->_hasMany[$name]) || isset($this->_hasOne[$name]) || isset($this->_belongsTo[$name]))) {
			$this->__get($name);
		}

		if (isset($this->_fields[$name]) || isset($this->_modified[$name])) {
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
		$watch_pk = true;
		if ( !$this->_pk_reset && isset($this->_fields[$name]) && $this->_fields[$name] === $value && isset($this->_modified[$name])) {
			unset($this->_modified[$name]);
		} elseif (!isset($this->_fields[$name])) {
			$this->_modified[$name] = $this->_fields[$name] = $value;
			// this is more likely due to the object initially being loaded
			// so we shouldn't need to array_merge() if this is the case
			$watch_pk = false;
		} else {
			$this->_modified[$name] = $value;
		}
		# in order to handle data copying
		if (
			$watch_pk
			&& (
				( is_array( static::$_pk ) && in_array( $name, static::$_pk ) )
				|| $name == static::$_pk
			)
		) {
			$this->_modified = array_merge( $this->_fields, $this->_modified );
			$this->_pk_reset = true;
		}
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
		if (empty(static::$_table)) static::$_table = get_parent_class();

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
		$pk = static::pk();

		if( !is_array( $pk ) ) {
			$pk = array( $pk );
		}

		$bindParams = array( );
		foreach( $pk as $key ) {
			$bindParams[$key] = $this->{$key};
		}

		$keyWhere = static::getKeyWhere( );

		$stmnt = $this->_db->prepare('DELETE FROM '.static::$_table.' WHERE '.$keyWhere);
		$stmnt->execute( $bindParams );
		return((bool)$stmnt->rowCount());
	}

	/**
	 * Get records from the table tied to the model.
	 *
	 * @param string $where WHERE clause of the SQL query.
	 * @param bool $only_one Set to true to only return a single record
	 * @return mixed
	 */
	public static function get($where = null, $only_one = false)
	{
		$sql = 'SELECT * FROM '.static::$_table;

		$bindParams = array( );

		$pk = static::pk( );
		if( is_array( $where ) && !is_array( $pk ) ) {
			$where = array_pop( $where );
		}

		if (!empty($where)) {
			if( is_array( $pk ) && count(explode('-', $where)) > 1 ) {
				if( !is_array( $where ) ) {
					$where = array_combine( $pk, explode( '-', $where ) );
				}

				if( count( $pk ) != count( $where ) ) {
					throw new SiTech_Exception( 'Invalid primary key specification in get for ' . get_called_class( ) );
				}

				$keyWhere = static::getKeyWhere( );
				$sql .= ' WHERE ' . $keyWhere;
				$bindParams = $where;
			} elseif (is_int($where) ) {
				$sql .= ' WHERE '.$pk.' = '.$where;
			} else {
				$sql .= ' WHERE '.$where;
			}
		}

		$stmnt = static::db()->query($sql, $bindParams);
		$stmnt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

		if ($only_one) {
			return($stmnt->fetch());
		} else {
			return($stmnt->fetchAll());
		}
	}

	public static function getCount($where = null)
	{
		$sql = 'SELECT COUNT(*) FROM '.static::$_table;

		$bindParams = array( );

		$pk = static::pk( );
		if( is_array( $where ) && !is_array( $pk ) ) {
			$where = array_pop( $where );
		}

		if (!empty($where)) {
			if( is_array( $pk ) ) {
				if( !is_array( $where ) ) {
					$where = array_combine( $pk, explode( '-', $where ) );
				}

				if( count( $pk ) != count( $where ) ) {
					throw new SiTech_Exception( 'Invalid primary key specification in getCount for ' . get_called_class( ) );
				}

				$keyWhere = static::getKeyWhere( );
				$sql .= ' WHERE ' . $keyWhere;
				$bindParams = $where;
			} elseif (is_int($where) ) {
				$sql .= ' WHERE '.$pk.' = '.$where;
			} else {
				$sql .= ' WHERE '.$where;
			}
		}

		$stmnt = static::db()->query($sql, $bindParams);
		return((int)$stmnt->fetchColumn());
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

		$id = $this->getId();
		$insert = $id ? static::getCount( $id ) == 0 : true;

		$save = false;
		if ($insert) {
			$save = $this->_insert();
		} else {
			$save = $this->_update();
		}

		if ( $save ) {
			$this->_fields = array_merge($this->_fields, $this->_modified);
			$this->_modified = array();
			$this->_pk_reset = false;
		}
		return($save);
	}

	public function toJson()
	{
		return(json_encode($this->_fields));
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
	protected function _insert()
	{
		$pk = static::pk();
		if ( !is_array( $pk ) ) {
			$pk = array( $pk );
		}

		$sql = 'INSERT INTO '.static::$_table.' ';
		$fields = array();
		$values = array();

#		$tmp_fields = array_merge( $this->_fields, $this->_modified );
		$tmp_fields = ( $this->_modified ?: $this->_fields );
		foreach ($tmp_fields as $f => $v) {
			// allow for manually setting primary keys!!! -- rmp
			if (in_array($f, $pk) && empty( $v )) continue;
			$fields[] = $f;
			// TODO what happens if $v is an array (i.e. has many)?
			$values[$f] = ($v instanceof SiTech_Model_Abstract)? $v->{$v::pk()} : $v;
		}

		$sql .= '('.implode(',', $fields).') VALUES(:'.implode(',:', $fields).')';
		$stmnt = $this->_db->prepare($sql);
		if ($stmnt->execute($values)) {
			// Assign the PK once the row is inserted
			foreach( $pk as $key ) {
				if( !empty( $tmp_fields[$key] ) )
					continue;
				$this->_modified[$key] = $this->_db->lastInsertId();
			}
		}
		return($stmnt->rowCount());
	}

	/**
	 * Update the existing record in the database based on the primary key that
	 * is specified. If the query returns rows affected, this will return true
	 * otherwise it will return false if no rows are affected.
	 *
	 * @return bool
	 */
	protected function _update()
	{
		$pk = static::pk();
		if ( !is_array( $pk ) ) {
			$pk = array( $pk );
		}

		$sql = 'UPDATE '.static::$_table.' SET ';
		$fields = array();
		$values = array();

		$tmp_fields = ( $this->_modified ?: $this->_fields );
#		foreach ($this->_modified as $f => $v) {
		foreach ($tmp_fields as $f => $v) {
			if (in_array( $f, $pk)) continue; // We don't update the value of the pk
			$fields[] = $f.' = :' . $f;
			$values[$f] = ($v instanceof SiTech_Model_Abstract)? $v->{$v::pk()} : $v;
		}

		$sql .= implode(',', $fields);
		$sql .= ' WHERE '.static::getKeyWhere( );
		foreach( $pk as $key ) {
#			$values[$key] = $this->_fields[$key];
			$values[$key] = $this->{$key};
		}
		$stmnt = $this->_db->prepare($sql);
		$stmnt->execute($values);
		return(($stmnt->rowCount() === false)? false : true);
	}

	protected static function getIdString( $tableName = '' ) {
		if( empty( $tableName ) )
			$tableName = static::$_table;

		if( is_array( static::$_pk ) ) {
			$pks = array( );
			foreach( static::$_pk as $pk ) {
				$pks[] = '`' . $tableName . '`.`' . $pk . '`';
			}
			$idString = implode( ', "-", ', $pks );
			$idString = 'CONCAT( ' . $idString . ' ) AS id';
		} else {
			$idString = '`' . $tableName . '`.`' . static::$_pk . '` AS id';
		}

		return $idString;
	}

	protected static function getKeyWhere( $tableName = '' ) {
		if( empty( $tableName ) )
			$tableName = static::$_table;

		if( is_array( static::$_pk ) ) {
			$keyWhereArray = array( );
			foreach( static::$_pk as $pk ) {
				$keyWhereArray[] = '`' . $tableName . '`.`' . $pk . '` = :' . $pk;
			}
			$keyWhere = implode( "\n\t\tAND ", $keyWhereArray );
		} else {
			$keyWhere = '`' . $tableName . '`.`' . static::$_pk . '` = :' . static::$_pk;
		}

		return $keyWhere;
	}
	
	/**
	 * If attempting to get a string value for the model, return the model id
	 *
	 * @return string id
	 */
	public function __toString( ) {
		return $this->getId( );
	}

	public function getId() {
		if( is_array( static::$_pk ) ) {	// primary key is composite
			$id = array();
			foreach( static::$_pk as $pk ) {
				if ( !isset($this->{$pk}) ) { return; }
				$id[] = $this->{$pk};
			}
			$id = implode( '-', $id );
			return $id;
		} elseif ( isset($this->{static::$_pk}) ) {
			return $this->{static::$_pk};
		}
	}
}
