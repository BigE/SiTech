<?php
/**
 * SiTech Session class.
 *
 * @package SiTech_Session
 * @version $Id$
 */

/**
 * SiTech base functionality
 */
require_once('SiTech.php');
SiTech::loadClass('SiTech_Factory');

/**
 * Session class to make session management easier and more secure.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_Session
 * @package SiTech_Session
 */
class SiTech_Session extends SiTech_Factory
{
	/**
	 * Class constant for file based sessions.
	 */
	const TYPE_FILE	= 'SiTech_Session_File';
	/**
	 * Class constant for database based sessions.
	 */
	const TYPE_DB	= 'SiTech_Session_DB';

	/**
	 * Constructor for session support.
	 *
	 * @param string $type This can be a string of the class name or one of the
	 * class constants defined in this class.
	 */
	public function __construct($type=null)
	{
		if (is_null($type)) {
			$type = self::TYPE_FILE;
		}

		SiTech::loadClass($type);
		$this->_backend = new $type();
	}
}
?>