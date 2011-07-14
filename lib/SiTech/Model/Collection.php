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
 * Once the collection is started
 */
const STATE_STARTED = 1;

/**
 * Once the collection is complete
 */
const STATE_COMPLETE = 2;

/**
 * Description of Collection
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class Collection extends Base implements \Countable, \Iterator
{
	/**
	 * Cached count of items found in the database for the collection. Will be
	 * false if no count has been made yet.
	 *
	 * @var int
	 */
	protected $_count = false;

	/**
	 * Each record that is fetched as part of the collection is stored here.
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Class name of the model used for the collection. This is optional.
	 *
	 * @var string
	 */
	protected static $_model;

	/**
	 * Current position of the iterator in the items in the collection.
	 *
	 * @var int
	 */
	protected $_position = 0;

	protected $_state = 0;

	/**
	 * The statement holder for our query that drives the collection.
	 *
	 * @var PDOStatement
	 */
	protected $_statement;

	protected $_where;

	public function __construct($where = null, $countKey = '*', \PDO $db = null)
	{
		if (empty($db) && !empty($this->_model)) {
			$db = call_user_func(array($this->_model, 'db'));
		}

		parent::__construct($db);
		if (empty(static::$_table)) {
			if (empty(static::$_model)) {
				// TODO: Make a custom exception to suit this error
				throw new Exception('You must set either the table or the model to query against.');
			} else {
				static::$_table = call_user_func(array(static::$_model, 'table'));
			}
		}

		if (!empty(static::$_model) && $countKey === '*') {
			try {
				$pk = call_user_func(array(static::$_model, 'pk'));
				$countKey = $pk;
			} catch (Exception $e) {}
		}

		$this->_key = $countKey;
		$this->_where = $where;
	}

	/**
	 * Get an array of records found in the collection.
	 *
	 * @return array
	 */
	public function all()
	{
		if (!$this->isStarted()) {
			$this->_start();
			$this->_data = $this->_statement->fetchAll();
		} elseif (!$this->isComplete()) {
			while ($this->fetch()) {}
		}

		return($this->_data);
	}

	public function current()
	{
		var_dump(__METHOD__);
		$offset = $this->_position;

		// If we haven't fetched anything yet, grab the first row
		if ($offset === 0 && !$this->isStarted()) $this->fetch();

		return($this->_data[$offset]);
	}

	/**
	 *
	 * @param bool $cached If set to false, the query will be run even if the
	 *                     count has already been retreived.
	 * @return int Returns false on failure
	 */
	public function count($cached = true)
	{
		var_dump(__METHOD__);
		if ($this->_count === false || $cached === false) {
			$pk = (!empty(static::$_model))? call_user_func(array(static::$_model, 'pk')) : '*';
			$sql = 'SELECT COUNT('.$pk.') FROM '.static::$_table.$this->_where();

			$stmnt = $this->_db->prepare($sql);
			$stmnt->execute($this->_whereArgs());
			$this->_count = (int)$stmnt->fetchColumn();
			$stmnt->closeCursor();
			unset($stmnt);
		}

		return($this->_count);
	}

	public function fetch()
	{
		var_dump(__METHOD__);
		if (!$this->isStarted()) {
			$this->_start();
		}

		if (!($record = $this->_statement->fetch())) {
			$this->_state = $this->_state | STATE_COMPLETE;
		} else {
			$this->_data[$this->_position] = $record;
		}

		return($record);
	}

	public function isStarted()
	{
		return((bool)($this->_state & STATE_STARTED));
	}

	public function isComplete()
	{
		return((bool)($this->_state & STATE_COMPLETE));
	}

	public function key()
	{
		var_dump(__METHOD__);
		return($this->_position);
	}

	public function next()
	{
		var_dump(__METHOD__);
		++$this->_position;
		if (!$this->isComplete()) $this->fetch();
	}

	public function rewind()
	{
		var_dump(__METHOD__);
		$this->_position = 0;
	}

	public function toJSON(array $fields = array())
	{
		if (!$this->isStarted() || !$this->isComplete()) {
			$this->all();
		}
	}

	public function valid()
	{
		var_dump(__METHOD__);
		if ($this->isStarted()) {
			return(!$this->isComplete() || $this->_position < count($this));
		} else {
			return(count($this));
		}
	}

	protected function _start()
	{
		$sql = 'SELECT * FROM '.static::table().$this->_where();
		$this->_statement = static::db()->prepare($sql);
		$this->_statement->execute($this->_whereArgs());

		if (isset(static::$_model)) {
			$this->_statement->setFetchMode(\PDO::FETCH_CLASS, static::$_model);
		} else {
			$this->_statement->setFetchMode(\PDO::FETCH_ASSOC);
		}

		$this->_state = $this->_state | STATE_STARTED;
	}

	protected function _where()
	{
		$where = '';

		if (is_array($this->_where)) {
			$where = ' WHERE '.$this->_where[0];
		} elseif (!empty($this->_where)) {
			$where = ' WHERE '.$this->_where;
		}

		return($where);
	}

	protected function _whereArgs()
	{
		$args = array();

		if (is_array($this->_where) && isset($this->_where[1])) {
			$args = $this->_where[1];
		}

		return($args);
	}
}
