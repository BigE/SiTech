<?php
/**
 * SiTech Database class
 *
 * @package SiTech_DB
 */

/**
 * Get SiTech base.
 */
require_once('SiTech.php');
SiTech::loadInterface('SiTech_DB_Interface');

/**
 * SiTech Database Base class. All base database functionality that is not
 * database specific is defined here.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Base
 * @package SiTech_DB
 */
abstract class SiTech_DB_Base implements SiTech_DB_Interface
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
     * Execute some SQL code and return the rows affected.
     *
     * @param string $sql SQL to execute
     * @return int
     */
    public function exec($sql)
    {
        $stmnt = $this->prepare($sql);
        $stmnt->execute();
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
     * Execute some SQL code and return either a statement or bool value
     *
     * @param string $sql SQL statement to execute
     * @param int $mode Fetch mode for statement
     * @param mixed $arg1 Depending on the fetch mode, this should be set to the specified value
     * @param mixed $arg2 Also dependant uppon the fetch mode
     * @return mixed
     */
    public function query($sql, $mode=null, $arg1=null, $arg2=null)
    {
        $stmnt = $this->prepare($sql);
        $stmnt->execute();
        return($stmnt);
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

    abstract protected function _connect();
    abstract protected function _disconnect();
}
?>
