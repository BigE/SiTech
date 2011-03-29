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

namespace SiTech\Session\Handler;

/**
 * Interface for all session handlers.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Session
 * @subpackage SiTech\Session\Handler
 * @version $Id$
 */
interface IHandler
{
	/**
	 * Close the currently open session.
	 *
	 * @return bool
	 */
	public function close();

	/**
	 * Delete the session entierly.
	 *
	 * @param string $id
	 * @return bool
	 */
	public function destroy($id);

	/**
	 * Do garbage cleanup.
	 *
	 * @return bool
	 */
	public function gc($maxLife);

	/**
	 * Open the session.
	 *
	 * @param string $path
	 * @param string $name
	 * @return bool
	 */
	public function open($path, $name);

	/**
	 * Read the session information.
	 *
	 * @param string $id
	 * @return string
	 */
	public function read($id);

	/**
	 * Write the session information.
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
	public function write($id, $data);
}

require_once('SiTech/Session.php');

/**
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Session
 * @subpackage SiTech\Session\Handler
 * @version $Id$
 */
class Exception extends \SiTech\Session\Exception {}
