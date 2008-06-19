<?php
/**
 * Contains the database session handler.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Session
 * @version $Id$
 */

/**
 * @see SiTech_Session_Handler_Interface
 */
require_once('SiTech/Session/Handler/Interface.php');

/**
 * Interface for all session handlers.
 *
 * @package SiTech_Session
 * @subpackage SiTech_Session_Handler
 */
class SiTech_Session_Handler_DB implements SiTech_Session_Handler_Interface
{
	/**
	 * Close the session.
	 *
	 * @return bool
	 */
	public function close ()
	{
		return(true);
	}

	/**
	 * Delete the session entierly.
	 *
	 * @param string $id
	 * @return bool
	 */
	public function destroy ($id)
	{
		$db = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_CONN);
		$table = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_TABLE);
		$stmnt = $db->prepare('DELETE FROM '.$table.' WHERE Name = :name AND Id = :id');
		return($stmnt->execute(array(':name' => $_SESSION->getAttribute(SiTech_Session::ATTR_NAME), ':id' => $id)));
	}

	/**
	 * Do garbage cleanup.
	 *
	 * @return bool
	 */
	public function gc ($maxLife)
	{
		$db = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_CONN);
		$table = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_TABLE);
		$maxLife = date('Y-m-d G:i:s', time() - $maxLife);
		$stmnt = $db->prepare('DELETE FROM '.$table.' WHERE Started < \''.$maxLife.'\' AND Remember = 0');
		$stmnt->execute();

		return(true);
	}

	/**
	 * Open the session.
	 *
	 * @param string $path
	 * @param string $name
	 * @return bool
	 */
	public function open ($path, $name)
	{
		$db = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_CONN);
		if (!($db instanceof SiTech_DB_Driver_Interface) && !($db instanceof SiTech_DB)) {
			require_once('SiTech/Session/Exception.php');
			throw new SiTech_Session_Exception('Cannot open session. Attribute SiTech_Session::ATTR_DB_CONN must instance of SiTech_DB_Driver_Interface');
		}

		$this->_savePath = $path;
		$_SESSION->setAttribute(SiTech_Session::ATTR_NAME, $name);
		return(true);
	}

	/**
	 * Read the session information.
	 *
	 * @param string $id
	 * @return string
	 */
	public function read ($id)
	{
		$db = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_CONN);
		$table = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_TABLE);
		$stmnt = $db->prepare('SELECT Id, Name, Data, Remember, Strict, RemoteAddr FROM '.$table.' WHERE Name=:name AND Id=:id');
		if ($stmnt->execute(array(':name' => $_SESSION->getAttribute(SiTech_Session::ATTR_NAME), ':id' => $id))) {
			$row = $stmnt->fetch();
			$_SESSION->setAttribute(SiTech_Session::ATTR_REMEMBER, (bool)$row['Remember']);
			$_SESSION->setAttribute(SiTech_Session::ATTR_STRICT, (bool)$row['Strict']);
			return(unserialize($row['Data']));
		} else {
			return('');
		}
	}

	/**
	 * Write the session information.
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
	public function write ($id, $data)
	{
		$db = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_CONN);
		$table = $_SESSION->getAttribute(SiTech_Session::ATTR_DB_TABLE);
		/*
		 * TODO: Fix this so we get the old value to reset it later. We have to se it to none here
		 * because this is usually called while the script is ending.
		 */
		$db->setAttribute(SiTech_DB::ATTR_ERRMODE, SiTech_DB::ERR_NONE);
		$stmnt = $db->prepare('SELECT Id FROM '.$table.' WHERE Name=:name AND Id=:id');
		$stmnt->execute(array(':name' => $_SESSION->getAttribute(SiTech_Session::ATTR_NAME), ':id' => $id));
		if ($stmnt->rowCount() > 0) {
			$stmnt = $db->prepare('UPDATE '.$table.' SET Data=:data, Remember=:remember, Strict=:strict, RemoteAddr=:remote WHERE Id=:id AND Name=:name');
		} else {
			$stmnt = $db->prepare('INSERT INTO '.$table.' (Id, Name, Data, Remember, Strict, RemoteAddr) VALUES(:id, :name, :data, :remember, :strict, :remote)');
		}

		$ret = $stmnt->execute(array(':id' => $id, ':name' => $_SESSION->getAttribute(SiTech_Session::ATTR_NAME), ':data' => serialize($data), ':remember' => (int)$_SESSION->getAttribute(SiTech_Session::ATTR_REMEMBER), ':strict' => (int)$_SESSION->getAttribute(SiTech_Session::ATTR_STRICT), ':remote' => $_SERVER['REMOTE_ADDR']));
		return($ret);
	}
}
