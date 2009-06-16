<?php
/**
 * SiTech/DB/Privilege/Abstract.php
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
 * SiTech_DB_Privilege_Abstract - Base class for all privilege classes based on
 * database type.
 *
 * @package SiTech_DB
 * @subpackage SiTech_DB_Privilege
 */
abstract class SiTech_DB_Privilege_Abstract
{
	/**
	 * SiTech_DB object holder
	 *
	 * @var SiTech_DB
	 */
	protected $pdo;

	protected $privileges = array();

	/**
	 * Constructor.
	 *
	 * @param SiTech_DB $pdo
	 */
	public function __construct(SiTech_DB $pdo)
	{
		$this->pdo = $pdo;
	}
}