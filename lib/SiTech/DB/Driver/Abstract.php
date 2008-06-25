<?php
abstract class SiTech_DB_Driver_Abstract
{
	static protected $instance;

	final static protected function _singleton($class)
	{
		if (empty(self::$instance)) {
			self::$instance = new $class;
		}

		return(self::$instance);
	}
}
