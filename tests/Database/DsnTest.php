<?php
class DBDsnTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var DBDsnTestMockDsn
	 */
	protected $_dsn;

	public function setUp()
	{
		$this->_dsn = new DBDsnTestMockDsn(['foo' => 'bar']);
	}

	/**
	 * @covers \SiTech\Database\Dsn::getDsn
	 * @covers \SiTech\Database\Dsn::__toString
	 */
	public function testGetDsn()
	{
		$this->assertEquals('dsn', $this->_dsn->getDsn());
		// this returns a call to getDsn, so if the previous fails, this will to... meh
		$this->assertEquals('dsn', (string)$this->_dsn);
	}
}

class DBDsnTestMockDsn extends \SiTech\Database\Dsn
{
	public function _generateDsn()
	{
		$this->_dsn = 'dsn';
	}
}