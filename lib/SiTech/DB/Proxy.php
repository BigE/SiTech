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
 *
 * @filesource
 */

namespace SiTech\DB;

/**
 * @see SiTech\DB
 */
require_once('SiTech/DB.php');

/**
 * This is a class that extends the base SiTech_DB class and adds a "proxy"
 * ability. It will detect the type of query coming through and assign the right
 * connection (reader or writer) to it.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\DB
 * @todo Finish documentation
 * @version $Id$
 */
class Proxy extends \SiTech\DB
{
	/**
	 * Attribute that will cause the queries to only use the writer if set to
	 * true.
	 */
	const ATTR_WRITEONLY = 12345678900;

	/**
	 * Tells us if we're inside of a transaction or not. All transactions will
	 * be executed on the writer.
	 *
	 * @var bool
	 */
	protected $_inTransaction = false;

	/**
	 * If set to true, we want to only user a writer for our queries.
	 *
	 * @var bool
	 */
	protected $_useWriteOnly = false;

	/**
	 * Holder for "read-only" connection.
	 *
	 * @var SiTech_DB
	 */
	protected $_readConn;

	/**
	 * Holder for "write-only" connection.
	 *
	 * @var SiTech_DB
	 */
	protected $_writeConn;

	/**
	 * Initalize both connections for the reader and writer. If no readers are
	 * initalized, the writer will be default for everything.
	 *
	 * @param array $config Array of configuration settings for the connections.
	 * @param array $writers Array of hosts that are dedicated as "writers"
	 * @param array $readers Array of hosts that are dedicated as "readers"
	 * @param string $driver
	 * @param array $options Options to pass to the connections.
	 * @see SiTech_DB
	 */
	public function __construct(array $config, array $writers, array $readers = array(), $driver = 'SiTech\DB\Driver\MySQL', array $options = array())
	{
		// If there are readers available, set one up.
		if (!empty($readers)) {
			$reader = $readers[\mt_rand(0, (\sizeof($readers) - 1))];
			$rconfig = $config;
			$rconfig['dsn'] = \sprintf($rconfig['dsn'], $reader);
			$this->_readConn = new \SiTech\DB($rconfig, $driver, $options);
		}

		if (empty($writers)) {
			throw new Exception('No writers specified. You must specify at least one writer to SiTech_DB_Proxy::__construct()');
		}

		// The writer will be the default if no readers are selected.
		$writer = $writers[\mt_rand(0, (\sizeof($writers) - 1))];
		$wconfig = $config;
		$wconfig['dsn'] = \sprintf($wconfig['dsn'], $writer);
		$this->_writeConn = new \SiTech\DB($wconfig, $driver, $options);
	}

	/**
	 * Begin a transaction in the database. This is reliant upon the base PDO
	 * method.
	 *
	 * @return bool
	 */
	public function beginTransaction() {
		$this->_inTransaction = true;
		return($this->_writeConn->beginTransaction());
	}

	/**
	 * End the transaction by executing all the changes made. This is reliant upon
	 * the base PDO method.
	 *
	 * @return bool
	 */
	public function commit() {
		$this->_inTransaction = false;
		return($this->_writeConn->commit());
	}

	/**
	 * Perform a delete query on the table specified.
	 *
	 * @param string $table
	 * @param string $where
	 * @return int
	 * @see SiTech_DB::delete
	 */
	public function delete($table, $where = null) {
		return($this->_writeConn->delete($table, $where));
	}

	/**
	 * Return the last error codes from the reader and writer.
	 *
	 * @return array
	 */
	public function errorCode() {
		return(array(
			'reader' => ((!empty($this->_readConn))? $this->_readConn->errorCode() : null),
			'writer' => $this->_writeConn->errorCode()
		));
	}

	/**
	 * Return the last error info from the reader and writer.
	 *
	 * @return array
	 */
	public function errorInfo() {
		return(array(
			'reader' => ((!empty($this->_readConn))? $this->_readConn->errorInfo() : null),
			'writer' => $this->_writeConn->errorInfo()
		));
	}

	/**
	 * Execute the query in the database and return the number of rows affected.
	 *
	 * @param string $statement SQL query to execute
	 * @param array $args Arguments to use in the query
	 * @return int Number of rows affected by the query
	 */
	public function exec($statement, array $args = array()) {
		if ($this->_readOnly($statement)) {
			return($this->_readConn->exec($statement, $args));
		} else {
			return($this->_writeConn->exec($statement, $args));
		}
	}

	/**
	 * This grabs the attribute from the reader and writer and returns it as
	 * an array so you can see each value.
	 *
	 * @param int $attribute Attribute to get value for
	 * @return array Value that attribute is set to
	 */
	public function getAttribute($attribute) {
		if ($attribute == self::ATTR_WRITEONLY) {
			return(array('writer' => $this->_useWriteOnly));
		} else {
			return(array(
				'reader' => ((!empty($this->_readConn))? $this->_readConn->getAttribute($attribute) : null),
				'writer' => $this->_writeConn->getAttribute($attribute)
			));
		}
	}

	/**
	 * Insert a number of values into the specified table.
	 *
	 * @param string $table Table name to execute query on
	 * @param array $bind Array of fields and values to use
	 * @return int Number of rows inserted
	 */
	public function insert($table, array $bind) {
		return($this->_writeConn->insert($table, $bind));
	}

	/**
	 * This tells us if we're currently in a transaction.
	 *
	 * @return bool
	 */
	public function inTransaction()
	{
		if (\method_exists($this->_writeConn, 'inTransaction')) {
			return($this->_writeConn->inTransaction());
		} else {
			return($this->_inTransaction);
		}
	}

	/**
	 * Get the last inserted ID of the record into the database.
	 *
	 * @param string $name Column name to pull ID from
	 * @return int
	 */
	public function lastInsertId($name = null) {
		return($this->_writeConn->lastInsertId($name));
	}

	/**
	 * Prepare a SQL statement to be executed on the database server.
	 *
	 * @param string $statement SQL query to be prepared.
	 * @param array $driver_options Options to pass to the statement
	 * @return PDOStatement Statement class prepared by the database.
	 */
	public function prepare($statement, $driver_options = array())
	{
		if ($this->_readOnly($statement)) {
			return($this->_readConn->prepare($statement, $driver_options));
		} else {
			return($this->_writeConn->prepare($statement, $driver_options));
		}
	}

	/**
	 * Execute a query on the database server and get the statement back.
	 *
	 * @param string $statement
	 * @param array $args
	 * @return PDOStatement
	 */
	public function query($statement, array $args = array()) {
		if ($this->_readOnly($statement)) {
			return($this->_readConn->query($statement, $args));
		} else {
			return($this->_writeConn->query($statement, $args));
		}
	}

	/**
	 * Perform a quote. We use the writer to perform this method.
	 *
	 * @param string $string
	 * @param int $parameter_type
	 * @return string
	 */
	public function quote($string, $parameter_type = null) {
		return($this->_writeConn->quote($string, $parameter_type));
	}

	/**
	 * Rollback all changes made to the database.
	 *
	 * @return bool
	 */
	public function rollBack() {
		$this->_inTransaction = false;
		return($this->_writeConn->rollBack());
	}

	/**
	 * Set an attribute on both the reader and writer connections. An array
	 * containing the previous value from both connections will be returned.
	 *
	 * @param int $attribute
	 * @param mixed $value
	 * @return array
	 */
	public function setAttribute($attribute, $value) {
		if ($attribute == self::ATTR_WRITEONLY) {
			$ret = $this->_isWriteOnly;
			$this->_useWriteOnly = (bool)$value;
			return($ret);
		} else {
			return(array(
				'reader' => ((!empty($this->_readConn))? $this->_readConn->setAttribute($attribute, $value) : null),
				'writer' => $this->_writeConn->setAttribute($attribute, $value)
			));
		}
	}

	/**
	 * Send an update query to the database.
	 *
	 * @param string $table
	 * @param array $bind
	 * @param string $where
	 * @return int
	 */
	public function  update($table, array $bind, $where = null) {
		return($this->_writeConn->update($table, $bind, $where));
	}
	/**
	 * Check if the query can be executed on a read-only server.
	 *
	 * @param string $statement
	 * @return bool
	 */
	protected function _readOnly($statement)
	{
		$ret = false;

		if (!$this->_inTransaction && $this->getAttribute(self::ATTR_WRITEONLY) !== true && !empty($this->_readConn)) {
			list($type,) = \explode($statement, ' ', 1);

			switch ($type) {
				case 'DESC':
				case 'DESCRIBE':
				case 'EXPLAIN':
				case 'HELP':
				case 'SELECT':
				case 'SHOW':
					$ret = true;
					break;
			}
		}

		return($ret);
	}
}

namespace SiTech\DB\Proxy;

/**
 * Proxy specific exception.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\DB
 * @subpackage SiTech\DB\Proxy
 * @version $Id$
 */
class Exception extends \SiTech\DB\Exception {}
