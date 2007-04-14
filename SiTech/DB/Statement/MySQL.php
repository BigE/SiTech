<?php
/**
 * SiTech Database Statement for MySQL.
 *
 * @package SiTech_DB
 */

/**
 * Grab SiTech base class
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_DB_Statement_Base');

/**
 * Database statement for MySQL.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Statement_MySQL
 * @package SiTech_DB
 */
class SiTech_DB_Statement_MySQL extends SiTech_DB_Statement_Base
{
	/**
	 * Close the database cursor and free the result.
	 */
    public function closeCursor()
    {
        mysql_free($this->_result);
    }

    /**
     * Get the number of columns present in the current cursor result.
     *
     * @return int
     */
    public function columnCount()
    {
        return(mysql_num_fields($this->_result));
    }

    /**
     * Get the ANSII-SQL error code returned by the server. Unsupported
     * by the mysql extension.
     */
    public function errorCode()
    {
        /* unsupported by the MySQL extension */
    }

    /**
     * Grab the most recent error number and string from the server.
     *
     * @return array
     */
    public function errorInfo()
    {
        return(array(
            mysql_errno($this->_conn),
            mysql_error($this->_conn)
        ));
    }

    /**
     * Execute the SQL statement that has been prepared.
     *
     * @param array $params Any parameters to set in the query itself.
     */
    public function execute($params=array())
    {
        if (!is_resource($this->_conn)) {
            SiTech::loadClass('SiTech_DB_Statement_Exception');
            throw new SiTech_DB_Statement_Exception('The connection to the database is not currently open.');
        }

        if (($this->_result = mysql_query($this->_sql)) === false) {
            SiTech::loadClass('SiTech_DB_Statement_Exception');
            throw new SiTech_DB_Statement_Exception('The query to the database failed.%sMySQL reported: (%d) %s', array("\n", mysql_errno(), mysql_error()));
        }
    }

    /**
     * Not implemented.
     *
     * @param unknown_type $column
     */
    public function getColumnMeta($column)
    {
    }

    /**
     * Get the number of rows returned by the result.
     *
     * @return int
     */
    public function rowCount()
    {
        $count = 0;
        if (($count = @mysql_affected_rows($this->_result)) === false) {
            $count = @mysql_num_rows($this->_result);
        }

        return($count);
    }

    /**
     * Fetch a single row from the result and return it.
     *
     * @param int $offset Row number to grab from the result.
     * @return array
     */
    protected function _fetch($offset)
    {
    	if (@mysql_data_seek($this->_result, $offset) === false) {
    		/*SiTech::loadClass('SiTech_DB_Statement_Exception');
    		throw new SiTech_DB_Statement_Exception('The specified row offset %d is invalid.', array($offset));*/
    		return(false);
    	}

        /* we use fetch_array so that the real fetch method gets all the info it needs */
        return(mysql_fetch_array($this->_result));
    }
}
?>
