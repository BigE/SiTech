<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(dirname(dirname(__FILE__)).'/SiTech_PHPUnit_Base.php');

/**
 * Test the SiTech database engine. This is a class that simply extends PDO.
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class EngineTest extends SiTech_PHPUnit_Base
{
	protected static $_db = null;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		require_once('SiTech/DB/Engine.php');
	}

	public static function tearDownAfterClass()
	{
		self::$_db = null;
	}

	public function testEngineConstructor()
	{
		self::$_db = new SiTech\DB\Engine(array('dsn' => 'sqlite::memory'));
		$this->assertTrue(is_a(self::$_db, 'SiTech\DB\Engine'));
	}

	public function testEngineConstructorException()
	{
		try {
			// Pass it an invalid array so we see the exception.
			self::$_db = new SiTech\DB\Engine(array());
		} catch (SiTech\DB\Exception $ex) {
			$this->assertEquals('Missing required DSN from config', $ex->getMessage());
			return;
		}

		$this->fail('Invalid exception received');
	}
}
