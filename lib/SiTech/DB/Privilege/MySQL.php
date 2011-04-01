<?php
/**
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
 * @filesource
 */

namespace SiTech\DB\Privilege;

/**
 * @see SiTech\DB\Privilege\Privilege\Base
 */
require_once('SiTech/DB/Privilege/Base.php');

/**
 * @see SiTech\DB\Privilege\Record\MySQL
 */
require_once('SiTech/DB/Privilege/Record/MySQL.php');

/**
 * MySQL class for getting privileges from the mysql table.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\DB
 * @subpackage SiTech\DB\Privilege
 * @todo Finish documentation for file
 * @version $Id$
 */
class MySQL extends Base
{
	public function __construct(\SiTech\DB $pdo, $user=null, $host=null)
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