<?php
/**
 * SiTech Database class
 *
 * @package SiTech_DB
 * @version $Id$
 */

/**
 * Get SiTech base.
 * @see SiTech
 */
require_once('SiTech.php');
/**
 * @see SiTech_DB
 */
SiTech::loadClass('SiTech_DB');
/**
 * @see SiTech_DB_Interface
 */
SiTech::loadInterface('SiTech_DB_Backend_Interface');

/**
 * SiTech Database Base class. All base database functionality that is not
 * database specific is defined here.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Backend_Base
 * @package SiTech_DB
 */
abstract class SiTech_DB_Backend_Base implements SiTech_DB_Backend_Interface
{
    /**
     * Connection resource
     *
     * @var resource
     */
    protected $_conn = null;

    /**
     * Array of DSN options.
     *
     * @var array
     */
    protected $_dsn = array();

    /**
     * The fetch mode set for the connection.
     *
     * @var int SiTech_DB::FETCH_* constant
     */
    protected $_fetchMode = array(SiTech_DB::FETCH_ASSOC, null, null);

    /**
     * Class constructor.
     *
     * @param array $dsn DSN array of parsed values.
     */
    public function __construct($dsn)
    {
    	if (!is_array($dsn)) {
    		SiTech::loadClass('SiTech_DB');
    		$this->_dsn = SiTech_DB::parseDsn($dsn);
    	} else {
        	$this->_dsn = $dsn;
    	}
    }

    /**
     * Delete a row from the database.
     *
     * @return int Number of rows deleted.
     */
    public function delete($table, $where=null)
    {
    	$sql = "DELETE FROM $table";
    	if (!empty($where))
    		$sql .= "WHERE $where";

    	return($this->exec($sql));
    }

    /**
     * Execute some SQL code and return the rows affected.
     *
     * @param string $sql SQL to execute
     * @return int
     */
    public function exec($sql, array $args=array())
    {
        $stmnt = $this->prepare($sql);
        $stmnt->execute($args);
        return($stmnt->rowCount());
    }

    /**
     * NOT IMPLEMENTED: Gets the value of an attribute that is set on the database
     * connection.
     *
     * @param int $attr Attribute to get the value of
     * @return mixed
     * @throws SiTech_Exception
     */
    public function getAttribute($attr)
    {
        SiTech::loadClass('SiTech_Exception');
        throw new SiTech_Exception('%s::%s is not implemented yet', __CLASS__, __FUNCTION__);
    }

    /**
     * Get the fetch mode currently set.
     *
     * @return int
     */
    public function getFetchMode()
    {
    	return($this->_fetchMode);
    }

    /**
     * Perform an insert query on the database.
     *
     * @param string $table
     * @param array $values An array where the key is the field name.
     * @return int
     */
    public function insert($table, array $data)
    {
    	$cols = array();
    	$vars = array();

    	foreach ($data as $field => $value) {
    		$cols[] = $field;
    		$vars[] = $value;
    	}

    	$sql = "INSERT INTO $table (".implode(', ', $cols).') VALUES ('.implode(', ', $vars).')';
    	$result = $this->exec($sql);
    	return($result);
    }

    /**
     * Execute some SQL code and return either a statement or bool value
     *
     * @param string $sql SQL statement to execute
     * @param int $mode Fetch mode for statement
     * @param mixed $arg1 Depending on the fetch mode, this should be set to the specified value
     * @param mixed $arg2 Also dependant uppon the fetch mode
     * @return SiTech_DB_Statement_Interface
     */
    public function query($sql, $mode=null, $arg1=null, $arg2=null)
    {
        $stmnt = $this->prepare($sql);
        $stmnt->execute();
        return($stmnt);
    }

    /**
     * Return a SiTech_DB_Select object to create a SELECT query.
     *
     * @return SiTech_DB_Select
     */
    public function select()
    {
    	return(new SiTech_DB_Select());
    }

    /**
     * NOT IMPLEMENTED: Sets an attribute on the database conection.
     *
     * @param int $attr Attribute to set
     * @param mixed $value Attribute value
     * @return bool
     * @throws SiTech_Exception
     */
    public function setAttribute($attr, $value)
    {
        SiTech::loadClass('SiTech_Exception');
        throw new SiTech_Exception('%s::%s is not implemented yet', __CLASS__, __FUNCTION__);
    }

    /**
     * Set the fetch mode for all queries performed on the database.
     *
     * @param int $mode SiTech_DB::FETCH_* constant
     * @param mixed $arg1
     * @param mixed $arg2
     */
    public function setFetchMode($mode, $arg1=null, $arg2=null)
    {
    	$this->_fetchMode = array($mode, $arg1, $arg2);
    }

    /**
     * Perform an update query on the database.
     *
     * @param string $table Table name to update rows.
     * @param array $set Array of field=>value pairs to update.
     * @param string $where Where clause to give update chriteria.
     * @return int Number of rows affected.
     */
    public function update($table, array $set, $where='')
    {
    	$fields = array();
    	foreach ($set as $field => $value) {
    		$fields[] = $field.'='.$this->quote($value);
    	}

    	$sql = "UPDATE $table SET ".implode(', ', $fields);
    	if (!empth($where)) {
    		$sql .= " WHERE $where";
    	}

    	return($this->exec($sql));
    }

    abstract protected function _connect();
    abstract protected function _disconnect();
}
?>
