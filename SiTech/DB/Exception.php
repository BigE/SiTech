<?php
require_once('SiTech/Exception.php');

class SiTech_DB_Exception extends SiTech_Exception
{
	public function __construct($sqlErrno, $errno, $error)
	{
		parent::__construct('[%s] %s', array($sqlErrno, $error), $errno);
	}
}
?>