<?php
/**
 * Contains SiTech_DB which is a PDO wrapper.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
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
	const DRIVER_MYSQL = 'SiTech_DB_Driver_MySQL';
	const DRIVER_SQLITE = 'SiTech_DB_Driver_SQLite';

	/**
	 * Instance of class implementing SiTech_DB_Driver_Interface
	 *
	 * @var object SiTech_DB_Driver_Interface
	 */
	protected $driver;

	/**
	 * Constructor. We initalize everything here as well as create the object
	 * for the driver.
	 *
	 * @param string $dsn PDO DSN
	 * @param string $driver SiTech_DB_Driver_* class name that implements
	 *                       SiTech_DB_Driver_Interface. This can either be a string
	 *                       that is the class name, or a SiTech_DB::DRIVER_*
	 *                       constant.
	 * @param string $user PDO Username
	 * @param string $password PDO Password
	 * @param array $options Array of config options to pass to PDO.
	 */
	public function __construct($dsn, $driver, $username = null, $password = null, array $options = array())
	{
		parent::__construct($dsn, $username, $password, $options);

		/* This can be reset in user code, but we prefer exceptions */
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		require_once(str_replace('_', '/', $driver).'.php');
		/* We use a singleton to simplify things. */
		$this->driver = call_user_func(array($driver, 'singleton'));
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
}
