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

namespace SiTech\Model\Collection;

const ATTR_MODEL = 1;
const ATTR_DB = 2;
const ATTR_COUNT_KEY = 3;

namespace SiTech\Model;

/**
 * @see SiTech\Model\Base
 */
require_once('SiTech/Model/Base.php');

/**
 * Once the collection fetch from the DB is started
 */
const STATE_STARTED = 1;

/**
 * Once the collection fetch from the DB is complete
 */
const STATE_COMPLETE = 2;

/**
 * The collection model is a way to get a collection of models out of the
 * database.
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

	private $__model;

	/**
	 * Current position of the iterator in the items in the collection.
	 *
	 * @var int
	 */
	protected $_position = 0;

	/**
	 * Bitwise variable that holds the current state of the collection.
	 *
	 * @var int
	 */
	protected $_state = 0;

	/**
	 * The statement holder for our query that drives the collection.
	 *
	 * @var PDOStatement
	 */
	protected $_statement;

	/**
	 * The where clause that was passed in through the constructor.
	 *
	 * @var mixed
	 */
	protected $_where;

	public function __construct($where = null, array $options = array())
	{
		$db = null;
		$countKey = '*';
		$this->__model = static::$_model;

		if (!isset($options[Collection\ATTR_DB]) && !empty(static::$_model)) {
			$db = call_user_func(array($this->_model, 'db'));
		} elseif (isset($options[Collection\ATTR_DB])) {
			$db = $options[Collection\ATTR_DB];
		}

		if (isset($options[Collection\ATTR_MODEL])) {
			$this->__model = $options[Collection\ATTR_MODEL];
		}

		parent::__construct($db);
		if (empty(static::$_table)) {
			if (empty($this->__model)) {
				require_once('SiTech/Model/Exception.php');
				throw new Exception('You must set either the table or the model to query against.');
			} else {
				static::$_table = call_user_func(array($this->__model, 'table'));
			}
		}

		if (!empty($this->__model) && !isset($options[Collection\ATTR_COUNT_KEY])) {
			try {
				$pk = call_user_func(array($this->__model, 'pk'));
				$countKey = $pk;
			} catch (Exception $e) {}
		} elseif (isset($options[Collection\ATTR_COUNT_KEY])) {
			$countKey = $options[Collection\ATTR_COUNT_KEY];
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
		if ($this->_count === false || $cached === false) {
			$sql = 'SELECT COUNT('.$this->_key.') FROM '.static::$_table.static::_where($this->_where);

			$stmnt = $this->_db->prepare($sql);
			$stmnt->execute(static::_whereArgs($this->_where));
			$this->_count = (int)$stmnt->fetchColumn();
			$stmnt->closeCursor();
			unset($stmnt);
		}

		return($this->_count);
	}

	public function fetch()
	{
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
		return($this->_position);
	}

	public function next()
	{
		++$this->_position;
		if (!$this->isComplete() && !isset($this->_data[$this->_position])) $this->fetch();
	}

	public function rewind()
	{
		$this->_position = 0;
	}

	public function toJson(array $fields = array())
	{
		if (!$this->isStarted() || !$this->isComplete()) {
			$this->all();
		}

		if (isset(static::$_model)) {
			$json = '[';
			$array = array();
			foreach ($this->_data as $data) {
				$array[] = $data->toJson($fields);
			}

			$json .= implode(',', $array);
			$json .= ']';
		} else {
			$array = array();
			foreach ($this->_data as $data) {
				if (!empty($fields)) {
					$record = array();
					foreach ($fields as $field) {
						$record[$field] = $data[$field];
					}

					$array[] = $record;
				} else {
					$array[] = $data;
				}
			}

			$json = json_encode($array);
		}

		return($json);
	}

	public function valid()
	{
		if ($this->isStarted()) {
			return(!$this->isComplete() || $this->_position < count($this));
		} else {
			return(count($this));
		}
	}

	protected function _start()
	{
		$sql = 'SELECT * FROM '.static::table().static::_where($this->_where);
		$this->_statement = static::db()->prepare($sql);
		$this->_statement->execute(static::_whereArgs($this->_where));

		if (!empty($this->__model)) {
			$this->_statement->setFetchMode(\PDO::FETCH_CLASS, $this->__model);
		} else {
			$this->_statement->setFetchMode(\PDO::FETCH_ASSOC);
		}

		$this->_state = $this->_state | STATE_STARTED;
	}
}
