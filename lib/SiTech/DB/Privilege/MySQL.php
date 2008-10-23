<?php
require_once('SiTech/DB/Privilege/Abstract.php');
require_once('SiTech/DB/Privilege/Record/MySQL.php');

class SiTech_DB_Privilege_MySQL extends SiTech_DB_Privilege_Abstract
{
	public function __construct(SiTech_DB $pdo, $user=null, $host=null)
	{
		parent::__construct($pdo);
		$stmnt = $this->pdo->prepare('SHOW GRANTS');
		$stmnt->execute();
		while ($priv = $stmnt->fetchObject('SiTech_DB_Privilege_Record_MySQL')) {
			$this->privileges[$priv->getDatabase()] = $priv;
		}
	}

	public function canCreateDatabase()
	{
		if (isset($this->privileges['*'])) {
			return($this->privileges['*']->canCreateDatabase());
		} else {
			return(false);
		}
	}

	public function canCreateTable($dbName)
	{
		if (isset($this->privileges['*']) && $this->privileges['*']->canCreateTable()) {
			return(true);
		}

		if (isset($this->privileges[$dbName])) {
			return($this->privileges[$dbName]->canCreateTable());
		} else {
			return(false);
		}
	}

	public function canCreateUser()
	{
		if (isset($this->privileges['*']) && $this->privileges['*']->canCreateUser()) {
			return(true);
		} else {
			return(false);
		}
	}
}