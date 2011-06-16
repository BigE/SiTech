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
 * Description of RawConfigParserTest
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class RawConfigParserTest extends SiTech_PHPUnit_Base
{
	/**
	 * @var SiTech\ConfigParser\RawConfigParser
	 */
	protected static $_config;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
		require_once('SiTech/ConfigParser/RawConfigParser.php');
		self::$_config = new \SiTech\ConfigParser\RawConfigParser();
	}

	public static function tearDownAfterClass()
	{
		self::$_config = null;
		@unlink(SITECH_TEST_FILES.DIRECTORY_SEPARATOR.'config.ini');
	}

	public function testAddSection()
	{
		$this->assertTrue(self::$_config->addSection('main'));
	}

	/**
	 * @depends testAddSection
	 * @expectedException SiTech\ConfigParser\DuplicateSectionException
	 */
	public function testAddDuplicateSection()
	{
		self::$_config->addSection('main');
	}

	public function testSet()
	{
		$this->assertTrue(self::$_config->set('main', 'foo', 'bar'));
	}

	public function testWrite()
	{
		$this->assertTrue(self::$_config->write(SITECH_TEST_FILES.DIRECTORY_SEPARATOR.'config.ini'));
		// I do this just to clear the array before we read, that way we ensure that reading gives us untainted results
		self::$_config = new \SiTech\ConfigParser\RawConfigParser();
	}
	
	/**
	 * @depends testWrite
	 */
	public function testRead()
	{
		$files = self::$_config->read(array(SITECH_TEST_FILES.DIRECTORY_SEPARATOR.'config.ini'));
		$this->assertEquals(array(SITECH_TEST_FILES.DIRECTORY_SEPARATOR.'config.ini' => true), $files);
		$this->assertEquals('bar', self::$_config->get('main', 'foo'));
	}

	/**
	 * @expectedException SiTech\ConfigParser\Exception
	 */
	public function testReadFail()
	{
		$files = self::$_config->read(array('test.ini'));
	}
}
