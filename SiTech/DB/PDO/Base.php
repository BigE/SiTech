<?php
/**
 * Base class for all PDO database types supported. Here all base functionality will be
 * defined.
 *
 * @package SiTech_DB
 * @version $Id$
 */

/**
 * @see SiTech
 */
require_once('SiTech.php');
/**
 * @see SiTech_DB_Base
 */
SiTech::loadClass('SiTech_DB_Base');

/**
 * Database backend that extends PDO to add some extra functionality not currently found
 * in the PDO classes.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_PDO_Base
 * @package SiTech_DB
 */
abstract class SiTech_DB_PDO_Base extends SiTech_DB_Base
{
	/**
	 * This will automatically turn off autocommit mode so any changes made to the database
	 * can be rolled back.
	 *
	 * @return bool
	 */
	public function beginTransaction()
	{
		return($this->_conn->beginTransaction());
	}

	/**
	 * Commits a transaction to the database. Any changes made after beginTransaction() will
	 * be saved to the database.
	 *
	 * @return bool
	 */
	public function commit()
	{
		return($this->_conn->commit());
	}

	public function errorCode()
	{
		return($this->_conn->errorCode());
	}

	public function errorInfo()
	{
		return($this->_conn->errorInfo());
	}

	public function exec($sql)
	{
		$stmnt = $this->prepare($sql);
		$stmnt->execute();
		return($stmnt->rowCount());
	}

	/**
	 * Set an attribute on the current database connection.
	 *
	 * @param int $attr SiTech_DB::ATTR_* constant
	 * @return bool
	 */
	public function getAttribute($attr)
	{
		return($this->_conn->getAttribute());
	}

	public function lastInsertId($name=null)
	{
		return($this->_conn->lastInsertId($name));
	}

	public function prepare($sql, $args=array())
	{
		SiTech::loadClass('SiTech_DB_Statment_PDO');
		$stmnt = new SiTech_DB_Statement_PDO($sql);
		return($stmnt);
	}

    public function query($sql, $mode=null, $arg1=null, $arg2=null)
    {
    	$stmnt = $this->prepare($sql);
    	$stmnt->execute();
    	return($stmnt);
    }

    public function quote($string, $mode=null)
    {
    	$this->_conn->quote($string, $mode);
    }

    public function rollBack()
    {
    	$this->_conn->rollBack();
    }

    /**
     * Set an attribute on the current connection.
     *
     * @param int $attr SiTech_DB::ATTR_* constant
     * @param mixed $value Value to set attribute
     */
    public function setAttribute($attr, $value)
    {
    	$this->_conn->setAttribute($attr, $value);
    }

	/**
	 * Private connection method. This opens the connection through the PDO backend to the
	 * database. If any problems are found, an exception is raised.
	 *
	 * @throws SiTech_DB_Exception
	 */
	protected function _connect()
	{
		if (!extension_loaded('pdo')) {
			SiTech::loadClass('SiTech_DB_Exception');
			throw new SiTech_DB_Exception('The PDO extension was not found but is required %s to work', array(__CLASS__));
		}

		try {
			$this->_conn = new PDO($this->_dsn(), $this->_dsn['user'], $this->_dsn['pass']);
		} catch (PDOException $ex) {
			SiTech::loadClass('SiTech_DB_Exception');
			throw new SiTech_DB_Exception($e->getMessage());
		}
	}

	protected function _disconnect()
	{
		$this->_conn = null;
	}

	/**
	 * Return a PDO DSN string to use to connect to the database.
	 *
	 * @return string
	 */
	protected function _dsn()
	{
		$type = substr($this->_dsn['type'], 4);
		$dsn = $this->_dsn;

		unset($dsn['user']);
		unset($dsn['pass']);
		unset($dsn['type']);
		unset($dsn['protocol']);
		unset($dsn['options']);

		foreach ($dsn as $key => $val) {
			if (empty($val)) {
				unset($dsn[$key]);
			} else {
				$dsn[$key] = "$key=$val";
			}
		}

		return($type.':'.implode(';', $dsn));
	}
}
?>
