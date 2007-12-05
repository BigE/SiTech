<?php
require_once('SiTech/Exception.php');

class SiTech_DB_Exception extends SiTech_Exception
{
	public function __construct($sqlState, $errno, $error)
	{
		parent::__construct('%s: (%d) %s', array($sqlState, $errno, $error));
	}
}
?>