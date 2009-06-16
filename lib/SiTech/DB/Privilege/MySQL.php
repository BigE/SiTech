<?php
/**
 * SiTech/DB/Privilege/MySQL.php
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
 * @package SiTech_DB
 * @subpackage SiTech_DB_Privilege
 * @todo Finish documentation for file
 * @version $Id$
 */

/**
 * @see SiTech_DB_Privilege_Abstract
 */
require_once('SiTech/DB/Privilege/Abstract.php');

/**
 * @see SiTech_DB_Privilege_Record_MySQL
 */
require_once('SiTech/DB/Privilege/Record/MySQL.php');

/**
 * SiTech_DB_Privilege_MySQL - MySQL class for getting privileges from the mysql
 * table.
 *
 * @package SiTech_DB
 * @subpackage SiTech_DB_Privilege
 */
class SiTech_DB_Privilege_MySQL extends SiTech_DB_Privilege_Abstract
{
	public function __construct(SiTech_DB $pdo, $user=null, $host=null)
	{
		parent::__construct($pdo);
		$stmnt = $this->pdo->prepare('SHOW GRANTS');
		$stmnt->execute();
		while ($priv = $stmnt->fetchObject('SiTech_DB_Privilege_Record_MySQL')) {
			$this->privileges[$priv->getDatabase()] = $priv;
		}
	}

	public function canCreateDatabase()
	{
		if (isset($this->privileges['*'])) {
			return($this->privileges['*']->canCreateDatabase());
		} else {
			return(false);
		}
	}

	public function canCreateTable($dbName)
	{
		if (isset($this->privileges['*']) && $this->privileges['*']->canCreateTable()) {
			return(true);
		}

		if (isset($this->privileges[$dbName])) {
			return($this->privileges[$dbName]->canCreateTable());
		} else {
			return(false);
		}
	}

	public function canCreateUser()
	{
		if (isset($this->privileges['*']) && $this->privileges['*']->canCreateUser()) {
			return(true);
		} else {
			return(false);
		}
	}
}