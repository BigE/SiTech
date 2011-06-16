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
	protected $_configParser;

	protected function setUp()
	{
		require_once('SiTech/ConfigParser/RawConfigParser.php');
		$this->_configParser = new \SiTech\ConfigParser\RawConfigParser();
	}
	
	/**
	 * @expectedException SiTech\ConfigParser\Exception
	 */
	public function testRead()
	{
		$files = $this->_configParser->read(array('test.ini'));
	}
}
