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
	 * @covers \SiTech\Database\Dsn::__construct
	 */
	public function testConstruct()
	{
		$dsn = new DBDsnTestMockDsn(['foo' => 'bar']);
		$this->assertArrayHasKey('foo', $this->readAttribute($dsn, '_config'));
	}

	/**
	 * @covers \SiTech\Database\Dsn::__get
	 */
	public function testMagicGet()
	{
		$this->assertEquals('bar', $this->_dsn->foo);
		$this->assertNull($this->_dsn->isnotset);
	}

	public function testMagicSet()
	{
		$this->_dsn->bar = 'baz';
		$this->assertEquals('baz', $this->readAttribute($this->_dsn, '_config')['bar']);
	}

	/**
	 * @covers \SiTech\Database\Dsn::__isset
	 */
	public function testMagicIsset()
	{
		$this->assertTrue(isset($this->_dsn->foo));
		$this->assertFalse(isset($this->_dsn->iamnotset));
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

	/**
	 * @covers \SiTech\Database\Dsn::offsetGet
	 * @expectedException \Exception
	 */
	public function testOffsetGet()
	{
		$this->assertEquals('bar', $this->_dsn->offsetGet('foo'));
		$this->assertEquals('bar', $this->_dsn->offsetGet(['foo', 'fooey']));
		$this->assertEquals('foo=bar', $this->_dsn->offsetGet('foo', true));
		$this->assertEquals('foo=bar', $this->_dsn->offsetGet(['foo', 'fooey'], true));
		$this->assertEmpty($this->_dsn->offsetGet('bar'));
		$this->_dsn->offsetGet('bar', false, true);
	}

	/**
	 * @covers \SiTech\Database\Dsn::offsetExists
	 */
	public function testOffsetExists()
	{
		$this->assertTrue($this->_dsn->offsetExists('foo'));
		$this->assertFalse($this->_dsn->offsetExists('bar'));
	}

	/**
	 * @covers \SiTech\Database\Dsn::offsetSet
	 */
	public function testOffsetSet()
	{
		$this->_dsn->offsetSet('shebang', 'yup');
		$this->assertEquals('yup', $this->_dsn->offsetGet('shebang'));
	}

	/**
	 * @covers \SiTech\Database\Dsn::offsetUnset
	 */
	public function testOffsetUnset()
	{
		$this->_dsn->offsetSet('unset', 'yup');
		$this->_dsn->offsetUnset('unset');
		$this->assertEmpty($this->_dsn->offsetGet('unset'));
	}
}

class DBDsnTestMockDsn extends \SiTech\Database\Dsn
{
	public function _generateDsn()
	{
		$this->_dsn = 'dsn';
	}
}