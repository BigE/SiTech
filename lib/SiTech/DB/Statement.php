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
 */

namespace SiTech\DB;

/**
 * This is made to write queries out instead of executing them.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group © 2010-2011
 * @filesource
 * @package SiTech\DB
 * @version $Id$
 */
class Statement extends \PDOStatement
{
}

namespace SiTech\DB\Statement;

require_once('SiTech\DB.php');
class Exception extends \SiTech\DB\Exception {}
