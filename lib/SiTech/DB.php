<?php
/**
 * SiTech/DB.php
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
 * @copyright SiTech Group (c) 2008-2010
 * @filesource
 * @package SiTech
 * @subpackage SiTech_DB
 * @version $Id$
 */

/**
 * SiTech_DB
 *
 * Database class that extends PDO and adds additional functionality. We also
 * override a few of the PDO methods to, in our opinion, improve them.
 *
 * @package SiTech_DB
 */
class SiTech_DB extends PDO
{
	const ATTR_TRACK_QUERIES = 1234567890;

	const DRIVER_MYSQL = 'SiTech_DB_Driver_MySQL';
	const DRIVER_SQLITE = 'SiTech_DB_Driver_SQLite';

	/**
	 * Instance of class implementing SiTech_DB_Driver_Interface
	 *
	 * @var object SiTech_DB_Driver_Interface
	 */
	protected $driver;

	private $_queries = array();

	/**
	 * Constructor. We initalize everything here as well as create the object
	 * for the driver.
	 *
	 * @param array $config Array for the constructor options for PDO.
	 * @param string $driver SiTech_DB_Driver_* class name that implements
	 *                       SiTech_DB_Driver_Interface. This can either be a string
	 *                       that is the class name, or a SiTech_DB::DRIVER_*
	 *                       constant.
	 * @param array $options Array of config options to pass to PDO.
	 */
	public function __construct(array $config, $driver = 'SiTech_DB_Driver_MySQL', array $options = array())
	{
		if (empty($config['dsn'])) {
			require_once('SiTech/Exception.php');
			throw new SiTech_Exception('Missing required DSN from config');
		}

		$username = empty($config['user'])? null : $config['user'];
		$password = empty($config['password'])? null : $config['password'];
		parent::__construct($config['dsn'], $username, $password, $options);

		/* This can be reset in user code, but we prefer exceptions */
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if (!class_exists($driver)) {
			require_once(str_replace('_', '/', $driver).'.php');
		}

		/* We use a singleton to simplify things. */
		$this->driver = call_user_func_array(array($driver, 'singleton'), array($this));
	}

	public function delete($table, $where=null)
	{
		$sql = 'DELETE FROM '.$table;
		if (!empty($where)) {
			$sql .= ' WHERE '.$where;
		}

		return($this->exec($sql));
	}

	/**
	 * Execute an SQL statement and return the number of affected rows. We
	 * created our own method here isntead of using PDO to allow for an array
	 * of options to be passed similar to PDO_Statement::execute()
	 *
	 * @param string $statement SQL statement to prepare and execute.
	 * @param array $args Array of arguments to use in the SQL statement.
	 * @return int Returns the number of rows that were modified or deleted by
	 *             the SQL statement. If no rows were affected 0 is returned. If
	 *             the function fails FALSE will be returned.
	 */
	public function exec($statement, $args = array())
	{
		$ret = false;
		$stmnt = $this->query($statement, $args);

		if ($stmnt) {
			$ret = $stmnt->rowCount();
			$stmnt->closeCursor();
		}

		return($ret);
	}

	/**
	 * Get the attribute from the database connection.
	 *
	 * @param int $attribute
	 * @return mixed
	 */
	public function getAttribute($attribute)
	{
		if ($attribute == self::ATTR_TRACK_QUERIES) {
			return(true);
		} else {
			return(parent::getAttribute($attribute));
		}
	}

	/**
	 * Builds a dsn from values given in the configuration. Needs to have the
	 * array keys of 'driver', 'host', & 'database' defined. The array key
	 * 'port' is optional.
	 *
	 * @param array $config The configuration array from which to pull driver, host, database, & port from.
	 * @return string A dsn string.
	 */
	public static function getDsn(array $config)
	{
		$ret = false;
		if (
			array_key_exists( 'driver', $config )
			&& array_key_exists( 'host', $config )
			&& array_key_exists( 'database', $config )
		) {
			if ( array_key_exists( 'port', $config ) ) {
				$port = ';port=' . $config['port'];
			}
			else {
				$port = '';
			}
			$ret = sprintf(
				'%s:host=%s%s;dbname=%s'
				,$config['driver']
				,$config['host']
				,$port
				,$config['database']
			);
		}
		return $ret;
	}

	/**
	 * Get privileges for the specified user. If no user is specified, then the
	 * user that connected will be used.
	 *
	 * @param string $user
	 * @param string $host
	 * @return SiTech_DB_Privilege_Abstract
	 */
	public function getPrivileges($user=null, $host=null)
	{
		return($this->driver->getPrivileges($user, $host));
	}
	
	public function getQueries()
	{
		return($this->_queries);
	}

	public function getQueryCount()
	{
		return(sizeof($this->_queries));
	}

	/**
	 * Get the current statement class name.
	 *
	 * @return string
	 */
	public function getStatementClass()
	{
		return($this->getAttribute(PDO::ATTR_STATEMENT_CLASS));
	}

	/**
	 * Insert a row into the database.
	 *
	 * @param string $table Table name.
	 * @param array $bind
	 * @return int ID of last insert. False if insert fails.
	 */
	public function insert($table, array $bind)
	{
		$vals = array();
		$cols = array_keys($bind);
		for ($i = 0; $i < sizeof($cols); $i++) {
			$vals[$i] = '?';
		}

		$sql = 'INSERT INTO '.$table.' ('.implode(', ', $cols).') VALUES('.implode(', ', $vals).')';
		if ($this->exec($sql, array_values($bind))) {
			return($this->lastInsertId());
		} else {
			return(false);
		}
	}

	/**
	 * Executes an SQL statement, returning a result set as a PDOStatement object
	 *
	 * @param string $statement SQL statement to prepare and execute.
	 * @param array $args Array of arguments to use in the query.
	 * @return PDOStatement
	 */
	public function query($statement, array $args = array())
	{
		if ((bool)$this->getAttribute(self::ATTR_TRACK_QUERIES)) {
			$this->_queries[] = $statement;
		}
		
		$stmnt = $this->prepare($statement);
		if ($stmnt->execute($args)) {
			return($stmnt);
		} else {
			return(false);
		}
	}

	/**
	 * Set the statement class to be used. This must be or extend the
	 * SiTech_DB_Statement class.
	 *
	 * @param string $class Statement class name to use.
	 * @return bool
	 */
	public function setStatementClass($class)
	{
		if ($class == 'SiTech_DB_Statement' || is_subclass_of($class, 'SiTech_DB_Statement')) {
			$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array($class, array($this)));
			return(true);
		}

		return(false);
	}

	/**
	 * Update existing rows in a database.
	 *
	 * @param string $table
	 * @param array $bind
	 * @param string $where
	 */
	public function update($table, array $bind, $where=null)
	{
		$values = array();
		foreach ($bind as $key => $val) {
			$values[] = $key . '=?';
		}

		$sql = 'UPDATE '.$table.' SET '.implode(', ', $values);
		if (!empty($where)) {
			$sql .= ' WHERE '.$where;
		}

		return($this->exec($sql, array_values($bind)));
	}
}
