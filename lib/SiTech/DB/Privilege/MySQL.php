<?php
require_once('SiTech/DB/Privilege/Abstract.php');

class SiTech_DB_Privilege_MySQL extends SiTech_DB_Privilege_Abstract
{
	protected $_matches;

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
			$matches = array('privileges' => $matches[1], 'target' => $matches[2], 'user' => $matches[3], 'host' => $matches[4], 'leftover' => trim($matches[5]));
			$this->_matches = $matches;
		}
	}
}