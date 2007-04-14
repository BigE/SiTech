<?php
/**
 * SiTech Session DB support.
 *
 * @pacakage SiTech_Session
 */

/**
 * Grab main SiTech class
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_Session_Base');

/**
 * SiTech Session support for database backends.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Session_DB
 * @pacakage SiTech_Session
 */
class SiTech_Session_DB extends SiTech_Session_Base
{
	protected $_db;

	protected $_fields = array(
		'sessId' => 'sessId',
		'startTime' => 'startTime',
		'remember' => 'remember',
		'secure' => 'secure',
		'data' => 'sessionData'
	);

	protected $_table = 'SiTech_Sessions';

	public function __construct($db)
	{
		if ($db instanceof SiTech_DB_Base) {
			$this->_db = $db;
		} else {
			SiTech::loadClass('SiTech_Session_Exception');
			throw new SiTech_Session_Exception('Constructor argument must be an instance of SiTech_DB');
		}

		parent::__construct();
	}

	/**
	 * Set the database field names for the table we're saving sessions to.
	 *
	 * @param string $sessId Field to store the session ID in.
	 * @param string $startTime Field to store the session start time in.
	 * @param string $remember Field to signify if the session should be remembered past GC.
	 * @param string $secure Field to signifiy if the session should be secure.
	 * @param string $data Field to store session data in.
	 */
	public function setFields($sessId, $startTime, $remember, $secure, $data)
	{
		$this->_fields['sessId'] = $sessId;
		$this->_fields['startTime'] = $startTime;
		$this->_fields['remember'] = $remember;
		$this->_fields['secure'] = $secure;
		$this->_fields['data'] = $data;
	}

	/**
	 * Set the table name to use when accessing session information.
	 *
	 * @param string $name Table name.
	 */
	public function setTable($name)
	{
		$this->_table = $name;
	}

	/***************************************************************************************
	 * DO NOT ACCESS ANY OF THESE METHODS DIRECTLY. THEY ARE IN PLACE FOR SESSION HANDLERS *
	 ***************************************************************************************/

	/**
	 * Close the session. This only returns true because we don't do anything else
	 * to the database.
	 *
	 * @return bool
	 */
	public function _close()
	{
		parent::_close();
		return(true);
	}

	/**
	 * Destroy the session so it is no longer available.
	 *
	 * @param string $id Session ID
	 * @return bool
	 */
	public function _destroy($id)
	{
		if ($this->_db->execute("DELETE FROM {$this->_table} WHERE {$this->_fields['sessId']}=$id LIMIT 1") === false) {
			return(false);
		} else {
			return(true);
		}
	}

	/**
	 * General Cleanup. Removes old sessions that aren't needed anymore. It will
	 * check against sessions that are supposed to be remembered.
	 *
	 * @param int $maxLife Maximum lifetime of session in seconds.
	 * @return bool
	 */
	public function _gc($maxLife)
	{
		if ($this->_db->execute("DELETE FROM {$this->_table} WHERE {$this->_fields['startTime']} <= $maxLife AND {$this->_fields['remember']} <> 1") === false) {
			return(false);
		} else {
			return(true);
		}
	}

	/**
	 * Open the session and get everything ready. We also chceck to make sure
	 * the table and fields exist in the database.
	 *
	 * @param string $path Not used.
	 * @param string $name Session name.
	 * @return bool
	 */
	public function _open($path, $name)
	{
		return(true);
	}

	/**
	 * Read the session data from the database and return it.
	 *
	 * @param string $id Session id.
	 * @return string
	 */
	public function _read($id)
	{
		$id = $this->_db->escape($id);
		$stmnt = $this->_db->query("SELECT {$this->_fields['data']}, {$this->_fields['remember']}, {$this->_fields['secure']} FROM {$this->_table} WHERE {$this->_fields['sessId']} = '$id' LIMIT 1");

		if ($stmnt->rowCount() > 0) {
			$row = $stmnt->fetch(0, SiTech_DB_Statement_Base::FETCH_NUM);
			$this->_remember = (bool)$row[1];
			$this->_strict = (bool)$row[2];
			return($row[0]);
		} else {
			return('');
		}
	}

	/**
	 * Write the session data to the database.
	 *
	 * @param string $id Session id.
	 * @param string $data Session data.
	 * @return bool
	 */
	public function _write($id, $data)
	{
		$rows = $this->_db->execute("SELECT {$this->_fields['sessId']} FROM {$this->_table} WHERE {$this->_fields['sessId']} = '$id'");

		if ($rows > 0) {
			$retVal = $this->_db->execute("UPDATE {$this->_table} SET {$this->_fields['data']}='$data' WHERE {$this->_fields['sessId']} = '$id'");
		} else {
			$retVal = $this->_db->execute("INSERT INTO {$this->_table} () VALUES()");
		}

		return(($retVal === false)? false : true);
	}
}
?>
