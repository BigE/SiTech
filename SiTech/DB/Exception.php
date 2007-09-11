<?php
require_once('SiTech/Exception.php');

class SiTech_DB_Exception extends SiTech_Exception
{
	public function __construct($error, $errno=0, $sqlErrno='HY000')
	{
		parent::__construct('[%s] %s', array($sqlErrno, $error), $errno);
	}
}
?>