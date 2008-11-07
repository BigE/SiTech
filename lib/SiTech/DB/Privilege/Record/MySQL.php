<?php
/**
 * Contains the MySQL class for each privilege record.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_DB
 * @subpackage SiTech_DB_Privilege_Record
 * @todo Finish documentation for file
 * @version $Id: Abstract.php 113 2008-11-07 09:18:11Z eric $
 */

/**
 * @see SiTech_DB_Privilege_Record_Abstract
 */
require_once('SiTech/DB/Privilege/Record/Abstract.php');

/**
 * SiTech_DB_Privilege_Record_MySQL - MySQL class for each privilege record that
 * is retreived from MySQL.
 *
 * @package SiTech_DB
 * @subpackage SiTech_DB_Privilege_Record
 */
class SiTech_DB_Privilege_Record_MySQL extends SiTech_DB_Privilege_Record_Abstract
{
	/**
	 * ALL [PRIVILEGES]	Grants all privileges at specified access level except GRANT OPTION
	 */
	const ALL = 'ALL PRIVILEGES';

	/**
	 * ALTER	Enables use of ALTER TABLE
	 */
	const ALTER = 'ALTER';


	/**
	 * ALTER ROUTINE	Enables stored routines to be altered or dropped
	 */
	const ALTER_ROUTINE = 'ALTER ROUTINE';

	/**
	 * CREATE	Enables use of CREATE TABLE
	 */
	const CREATE = 'CREATE';

	/**
	 * CREATE ROUTINE	Enables creation of stored routines
	 */
	const CREATE_ROUTINE = 'CREATE ROUTINE';

	/**
	 * CREATE TEMPORARY TABLES	Enables use of CREATE TEMPORARY TABLE
	 */
	const CREATE_TEMPORARY_TABLES = 'CREATE TEMPORARY TABLE';

	/**
	 * CREATE USER	Enables use of CREATE USER, DROP USER, RENAME USER, and REVOKE ALL PRIVILEGES.
	 */
	const CREATE_USER = 'CREATE USER';

	/**
	 * CREATE VIEW	Enables use of CREATE VIEW
	 */
	const CREATE_VIEW = 'CREATE VIEW';

	/**
	 * DELETE	Enables use of DELETE
	 */
	const DELETE = 'DELETE';

	/**
	 * DROP	Enables use of DROP TABLE
	 */
	const DROP = 'DROP';

	/**
	 * EXECUTE	Enables the user to run stored routines
	 */
	const EXECUTE = 'EXECUTE';

	/**
	 * FILE	Enables use of SELECT ... INTO OUTFILE and LOAD DATA INFILE
	 */
	const FILE = 'FILE';

	/**
	 * INDEX	Enables use of CREATE INDEX and DROP INDEX
	 */
	const INDEX = 'INDEX';

	/**
	 * INSERT	Enables use of INSERT
	 */
	const INSERT = 'INSERT';

	/**
	 * LOCK TABLES	Enables use of LOCK TABLES on tables for which you have the SELECT privilege
	 */
	const LOCK_TABLES = 'LOCK TABLES';

	/**
	 * PROCESS	Enables the user to see all processes with SHOW PROCESSLIST
	 */
	const PROCESS = 'PROCESS';

	/**
	 * REFERENCES	Not implemented
	 */
	const REFERENCES = 'REFERENCES';

	/**
	 * RELOAD	Enables use of FLUSH
	 */
	const RELOAD = 'RELOAD';

	/**
	 * REPLICATION CLIENT	Enables the user to ask where slave or master servers are
	 */
	const REPLICATION_CLIENT = 'REPLICATION CLIENT';

	/**
	 * REPLICATION SLAVE	Needed for replication slaves (to read binary log events from the master)
	 */
	const REPLICATION_SLAVE = 'REPLICATION SLAVE';

	/**
	 * SELECT	Enables use of SELECT
	 */
	const SELECT = 'SELECT';

	/**
	 * SHOW DATABASES	SHOW DATABASES shows all databases
	 */
	const SHOW_DATABASES = 'SHOW DATABASES';

	/**
	 * SHOW VIEW	Enables use of SHOW CREATE VIEW
	 */
	const SHOW_VIEW = 'SHOW VIEW';

	/**
	 * SHUTDOWN	Enables use of mysqladmin shutdown
	 */
	const SHUTDOWN = 'SHUTDOWN';

	/**
	 * SUPER	Enables use of CHANGE MASTER, KILL, PURGE MASTER LOGS, and SET GLOBAL statements, the mysqladmin debug command; allows you to connect (once) even if max_connections is reached
	 */
	const SUPER = 'SUPER';

	/**
	 * UPDATE	Enables use of UPDATE
	 */
	const UPDATE = 'UPDATE';

	/**
	 * USAGE	Synonym for “no privileges”
	 */
	const USAGE = 'USAGE';

	/**
	 * GRANT OPTION	Enables privileges to be granted
	 */
	const GRANT_OPTION = 'GRANT OPTION';

	/**
	 * False if user has no permissions. True if user has global permissions,
	 * otherwise it will contain the database name.
	 *
	 * @var string
	 */
	protected $database = false;

	protected $privileges = array();

	protected $host;

	protected $user;

	public function __set($name, $val)
	{
		if (strstr($name, 'Grants for') === false) {
			/* not sure what this would be? */
			return;
		}

		/* At this point, I don't forsee a need to match any further. We just get the
		   permissions of the current user. */
		$matches = array();
		if (preg_match('#^GRANT (.*) ON ([^\s]+) TO \'([^\']*)\'@\'([^\']*)\'(.*)$#i', $val, $matches)) {
			$this->_parseTargets($matches[2]);
			$this->_parsePrivs($matches[1]);
			$this->user = $matches[3];
			$this->host = $matches[4];
		}
	}

	public function canCreateDatabase()
	{
		if (isset($this->privileges[self::ALL]) || isset($this->privileges[self::CREATE])) {
			return(true);
		} else {
			return(false);
		}
	}

	public function canCreateUser()
	{
		if ($this->hasAllPrivs() || isset($this->privileges[self::CREATE_USER])) {
			return(true);
		} else {
			return(false);
		}
	}

	public function canCreateTable()
	{
		if (isset($this->privileges[self::ALL]) || isset($this->privileges[self::CREATE])) {
			return(true);
		} else {
			return(false);
		}
	}

	public function getDatabase()
	{
		return(($this->database === true)? '*' : $this->database);
	}

	public function hasAllPrivs()
	{
		if (isset($this->privileges[self::ALL])) {
			return(true);
		} else {
			return(false);
		}
	}

	protected function _parsePrivs($privs)
	{
		$privs = explode(', ', $privs);

		foreach ($privs as $priv) {
			switch ($priv) {
				case 'ALL':
					$priv = self::ALL;
					break;
			}

			$this->privileges[$priv] = true;
		}
	}

	protected function _parseTargets($targets)
	{
		$targets = explode(', ', $targets);

		foreach ($targets as $target) {
			if (preg_match('#^`?([^`]+|\*)`?\.`?([^`]+|\*)`?$#', $target, $matches)) {
				if ($matches[1] == '*') {
					$this->database = true;
				} else {
					$this->database = $matches[1];
				}

				if ($matches[2] == '*') {
					$this->table = true;
				} else {
					$this->table = $matches[2];
				}
			}
		}
	}
}