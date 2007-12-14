<?php
require_once('SiTech/DB/Select/Interface.php');

class SiTech_DB_Select implements SiTech_DB_Select_Interface
{
	private $_distinct = false;
	
	public function __construct()
	{
	}
	
	public function __toString()
	{
		$sql = 'SELECT';
		if ($this->_distinct) {
			$sql .= ' DISTINCT';
		}
		
		$sql .= "\n\t";
		return($sql);
	}
	
	public function distinct($bool = true)
	{
		$this->_distinct = (bool)$bool;
	}
}
?>