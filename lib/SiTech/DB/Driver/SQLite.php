<?php
require_once('SiTech/DB/Driver/Abstract.php');

class SiTech_DB_Driver_SQLite extends SiTech_DB_Driver_Abstract
{
	static public function singleton()
	{
		return(self::_singleton(__CLASS__));
	}
}
