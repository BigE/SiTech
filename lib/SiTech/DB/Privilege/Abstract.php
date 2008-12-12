<?php
/**
 * Contains the base class for all privilege classes.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_DB
 * @subpackage SiTech_DB_Privilege
 * @todo Finish documentation for file
 * @version $Id$
 */

/**
 * SiTech_DB_Privilege_Abstract - Base class for all privilege classes based on
 * database type.
 *
 * @package SiTech_DB
 * @subpackage SiTech_DB_Privilege
 */
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