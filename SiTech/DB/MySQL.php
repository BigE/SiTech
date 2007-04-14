<?php
/**
 * SiTech Database MySQL class
 *
 * @package SiTech_DB
 */

/**
 * Get SiTech base.
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_DB_Base');

/**
 * SiTech Database MySQL class.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_MySQL
 * @package SiTech_DB
 */
class SiTech_DB_MySQL extends SiTech_DB_Base
{
	/**
	 * Begin a SQL transaction.
	 *
	 * @return bool
	 */
    public function beginTransaction()
    {
        if ($this->prepare('BEGIN')->execute() === false) {
            return(false);
        } else {
            return(true);
        }
    }

    /**
     * Commit (finish) a SQL transaction.
     *
     * @return bool
     */
    public function commit()
    {
        if ($this->prepare('COMMIT')->execute() === false) {
            return(false);
        } else {
            return(true);
        }
    }

    /**
     */
    public function errorCode()
    {
    }

    /**
     * Get the last error number and string from the database.
     *
     * @return array
     */
    public function errorInfo()
    {
    	return(array(
    		mysql_errno(),
    		mysql_error()
    	));
    }

    /**
     * Get the last ID inserted into the row. If the row name is not present, we just
     * attempt to get the ID from the insert_id function.
     *
     * @param string $name Row name to get value from.
     * @return mixed
     * @todo Need to add functionality to grab the specified column from the table.
     */
    public function lastInsertId($name=null)
    {
    	if (is_null($name)) {
    		return(mysql_insert_id());
    	} else {
    		/* TODO: Finish this part */
    	}
    }

    /**
     * Prepare a SQL string for execution. This does not execute the query yet.
     *
     * @param string $sql SQL string to prepare.
     * @param array $args Additional arguments to use in the query.
     * @return SiTech_DB_Statement_MySQL
     */
    public function prepare($sql, $args=array())
    {
        if (!is_resource($this->_conn)) {
            $this->_connect();
        }

        SiTech::loadClass('SiTech_DB_Statement_MySQL');
        $stmnt = new SiTech_DB_Statement_MySQL($sql, $this->_conn, $args);
        return($stmnt);
    }

    /**
     * Add quotes to a value so it is safe to be used in the SQL query.
     *
     * @param string $string Value to add quotes too.
     * @param unknown_type $mode
     * @return string
     */
    public function quote($string, $mode=null)
    {
        return(mysql_real_escape_string($string));
    }

    /**
     * Rollback (forget) changes made by transaction to the database.
     *
     * @return bool
     */
    public function rollBack()
    {
        if ($this->prepare('ROLLBACK')->execute() === false) {
            return(false);
        } else {
            return(true);
        }
    }

    /**
     * Make a connection to the database.
     *
     * @throws SiTech_DB_Exception
     */
    protected function _connect()
    {
    	if (isset($this->_dsn['port'])) {
    		$host = $this->_dsn['host'].':'.$this->_dsn['port'];
    	} else {
    		$host = $this->_dsn['host'];
    	}

        if (($this->_conn = mysql_connect($host, $this->_dsn['user'], $this->_dsn['pass'])) === false) {
            SiTech::loadClass('SiTech_DB_Exception');
            throw new SiTech_DB_Exception('Failed to connect to MySQL database.%sMySQL reported: (%d) %s', array("\n", mysql_errno(), mysql_error()));
        }

        if (mysql_select_db($this->_dsn['dbname']) === false) {
        	SiTech::loadClass('SiTech_DB_Exception');
        	throw new SiTech_DB_Exception('Failed to select MySQL database "%s"%sMySQL reported: (%d) %s', array($this->_dsn['dbname'], "\n", mysql_errno(), mysql_error()));
        }
    }

    /**
     * Close the connection to the database if one is active.
     */
    protected function _disconnect()
    {
        if (is_resource($this->_conn)) {
            mysql_close($this->_conn);
        }

        $this->_conn = null;
    }
}
?>
