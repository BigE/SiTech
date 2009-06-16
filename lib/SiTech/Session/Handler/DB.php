<?php
/**
 * SiTech/Session/Handler/DB.php
 *
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
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2009
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
	 * Database object holder.
	 *
	 * @var SiTech_DB
	 */
	protected $db;

	/**
	 * Table name to store session.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Constructor.
	 *
	 * This specific handler requires that a database object and table name be
	 * sent to the constructor for it to work properly.
	 *
	 * @param SiTech_DB $dbObj Database object for use in the handler.
	 * @param string $table Table name in the database to use.
	 */
	public function __construct($dbObj, $table)
	{
		/* sanity checks */
		if (!is_object($dbObj) && !is_a($dbObj, 'SiTech_DB')) {
			throw new Exception('The DB connection must be an instance or subclass of SiTech_DB');
		}

		$this->db = $dbObj;
		$this->table = $table;
	}

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
		$stmnt = $this->db->prepare('DELETE FROM '.$this->table.' WHERE Name = :name AND Id = :id');
		$session = SiTech_Session::singleton();
		return($stmnt->execute(array(':name' => $session->getAttribute(SiTech_Session::ATTR_SESSION_NAME), ':id' => $id)));
	}

	/**
	 * Do garbage cleanup.
	 *
	 * @return bool
	 */
	public function gc ($maxLife)
	{
		$maxLife = date('Y-m-d G:i:s', time() - $maxLife);
		$stmnt = $this->db->prepare('DELETE FROM '.$this->table.' WHERE Started < \''.$maxLife.'\' AND Remember = 0');
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
		$this->_savePath = $path;
		$session = SiTech_Session::singleton();
		$session->setAttribute(SiTech_Session::ATTR_SESSION_NAME, $name);
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
		$stmnt = $this->db->prepare('SELECT Id, Name, Data, Remember, Strict, RemoteAddr FROM '.$this->table.' WHERE Name=:name AND Id=:id');
		$session = SiTech_Session::singleton();
		if ($stmnt->execute(array(':name' => $session->getAttribute(SiTech_Session::ATTR_SESSION_NAME), ':id' => $id))) {
			$row = $stmnt->fetch();
			$session = SiTech_Session::singleton();
			$session->setAttribute(SiTech_Session::ATTR_REMEMBER, (bool)$row['Remember']);
			$session->setAttribute(SiTech_Session::ATTR_STRICT, (bool)$row['Strict']);
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
        $old_mode = $this->db->getAttribute(SiTech_DB::ATTR_ERRMODE);
		$this->db->setAttribute(SiTech_DB::ATTR_ERRMODE, SiTech_DB::ERR_NONE);
		$stmnt = $this->db->prepare('SELECT Id FROM '.$this->table.' WHERE Name=:name AND Id=:id');
		$session = SiTech_Session::singleton();
		$stmnt->execute(array(':name' => $session->getAttribute(SiTech_Session::ATTR_SESSION_NAME), ':id' => $id));
		if ($stmnt->rowCount() > 0) {
			$stmnt = $this->db->prepare('UPDATE '.$this->table.' SET Data=:data, Remember=:remember, Strict=:strict, RemoteAddr=:remote WHERE Id=:id AND Name=:name');
		} else {
			$stmnt = $this->db->prepare('INSERT INTO '.$this->table.' (Id, Name, Data, Remember, Strict, RemoteAddr) VALUES(:id, :name, :data, :remember, :strict, :remote)');
		}

		$remote = (isset($_SERVER['REMOTE_ADDR']))? $_SERVER['REMOTE_ADDR'] : null;
		$ret = $stmnt->execute(array(':id' => $id, ':name' => $session->getAttribute(SiTech_Session::ATTR_SESSION_NAME), ':data' => serialize($data), ':remember' => (int)$session->getAttribute(SiTech_Session::ATTR_REMEMBER), ':strict' => (int)$session->getAttribute(SiTech_Session::ATTR_STRICT), ':remote' => $remote));
        $this->db->setAttribute(SiTech_DB::ATTR_ERRMODE, $old_mode);
		return($ret);
	}
}
