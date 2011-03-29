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
 * Base class for all privilege classes based on database type.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\DB
 * @subpackage SiTech\DB\Privilege
 * @todo Finish documentation for file
 * @version $Id$
 */
abstract class APrivilege
{
	/**
	 * SiTech_DB object holder
	 *
	 * @var SiTech\DB
	 */
	protected $pdo;

	protected $privileges = array();

	/**
	 * Constructor.
	 *
	 * @param SiTech\DB $pdo
	 */
	public function __construct(SiTech\DB $pdo)
	{
		$this->pdo = $pdo;
	}
}