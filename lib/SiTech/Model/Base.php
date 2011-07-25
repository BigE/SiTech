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
 * I've had vodka, and this is the result. pwn.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Model
 * @todo Finish documentation and fix any remaining bugs.
 * @version $Id$
 */
abstract class Base
{
	/**
	 * PDO storage holder for the database connection.
	 *
	 * @var PDO
	 */
	protected static $db = array('default' => null);

	/**
	 * Database holder once the object is created.
	 *
	 * @var PDO
	 */
	protected $_db;

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

	/**
	 * Nothing much going on here, just set the database object to be used with
	 * the model so we have a way of accessing everything.
	 *
	 * @param \PDO $db Database connection to use with the model.
	 */
	public function __construct(\PDO $db = null)
	{
		if (empty($db)) {
			$this->_db = static::db();
		} else {
			$this->_db = $db;
		}
	}

	/**
	 * Get or set the database connection for the model to use. If no connection
	 * is set and you call this method without specifying a connection, an
	 * exception will be thrown.
	 *
	 * @param PDO $db Database connection to use
	 * @return PDO Only returns when no connection is passed in
	 * @throws SiTech\Exception
	 */
	public static function db(\PDO $db = null)
	{
		/**
		 * This is kinda like an init class since our internal methods use it,
		 * so lets do some basic checks.
		 */
		if (empty(static::$_table)) static::$_table = \get_parent_class();

		$class = get_called_class();
		$key   = ($class == 'SiTech\Model\Base') ? 'default' : $class;

		$ret = !isset(static::$db[$key]) ? static::$db['default'] : $db[$key];

		if (empty($db) && !is_a($ret, 'PDO')) {
			require_once('SiTech/Model/Exception.php');
			throw new Exception('The %s::$_db property is not set. Please use %s::db() to set the PDO connection.', array(\get_parent_class(), \get_parent_class()));
		} elseif (empty($db)) {
			return($ret);
		} else {
			static::$db[$key] = $db;
		}
	}

	/**
	 * Set or get the primary key field of the model.
	 *
	 * @param string $pk Primary key field to use for the model.
	 * @return string Will only return if no primary key is passed in.
	 */
	public static function pk($pk = null)
	{
		if (empty($pk) && empty(static::$_pk)) {
			require_once('SiTech/Model/Exception.php');
			throw new Exception('%s::$_pk is not set. Please use %s::pk() to set the primary key field.', array(get_parent_class(), get_parent_class()));
		} elseif (!empty($pk)) {
			static::$_pk = $pk;
		} else {
			return(static::$_pk);
		}
	}

	/**
	 *
	 * @param string $table Table name to use with the model.
	 * @return string Will return the table used if no table name is passed in.
	 */
	public static function table($table = null)
	{
		if (!empty($table)) {
			static::$_table = $table;
		} else {
			return(static::$_table);
		}
	}

	protected static function _where($where)
	{
		$ret = '';

		if (is_array($where)) {
			$ret = ' WHERE '.$where[0];
		} elseif (!empty($where)) {
			$ret = ' WHERE '.$where;
		}

		return($ret);
	}

	protected static function _whereArgs($where)
	{
		$args = array();

		if (is_array($where) && isset($where[1])) {
			$args = $where[1];
		}

		return($args);
	}
}
