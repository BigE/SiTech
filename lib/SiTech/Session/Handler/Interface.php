<?php
/**
 * Contains the session handler interface.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Session
 * @version $Id$
 */

/**
 * Interface for all session handlers.
 *
 * @package SiTech_Session
 * @subpackage SiTech_Session_Handler
 */
interface SiTech_Session_Handler_Interface
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
