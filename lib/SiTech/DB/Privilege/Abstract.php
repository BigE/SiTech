<?php
abstract class SiTech_DB_Privilege_Abstract
{
	/**
	 * SiTech_DB object holder
	 *
	 * @var SiTech_DB
	 */
	protected $pdo;

	protected $privileges = array();

	/**
	 * Constructor.
	 *
	 * @param SiTech_DB $pdo
	 */
	public function __construct(SiTech_DB $pdo)
	{
		$this->pdo = $pdo;
	}
}