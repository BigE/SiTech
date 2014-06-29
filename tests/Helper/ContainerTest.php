<?php

class ContainerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var \SiTech\Helper\Container
	 */
	protected $_container;

	public function setUp()
	{
		$this->_container = new Container(['foo' => 'bar']);
	}
	/**
	 * @covers \SiTech\Helper\Container::__construct
	 */
	public function testConstruct()
	{
		$container = new Container(['foo' => 'bar']);
		$this->assertInstanceOf('Container', $container);
		$this->assertArrayHasKey('foo', $this->readAttribute($container, 'attributes'));
	}

	/**
	 * @covers \SiTech\Helper\Container::count
	 */
	public function testCount()
	{
		$this->assertEquals(1, $this->_container->count());
	}

	/**
	 * @covers \SiTech\Helper\Container::offsetGet
	 * @expectedException \Exception
	 */
	public function testOffsetGet()
	{
		$this->assertEquals('bar', $this->_container->offsetGet('foo'));
		$this->assertEquals('bar', $this->_container->offsetGet(['foo', 'fooey']));
		$this->assertEquals('foo=bar', $this->_container->offsetGet('foo', true));
		$this->assertEquals('foo=bar', $this->_container->offsetGet(['foo', 'fooey'], true));
		$this->assertEmpty($this->_container->offsetGet('bar'));
		$this->_container->offsetGet('bar', false, true);
	}

	/**
	 * @covers \SiTech\Helper\Container::offsetExists
	 */
	public function testOffsetExists()
	{
		$this->assertTrue($this->_container->offsetExists('foo'));
		$this->assertFalse($this->_container->offsetExists('bar'));
	}

	/**
	 * @covers \SiTech\Helper\Container::offsetSet
	 */
	public function testOffsetSet()
	{
		$this->_container->offsetSet('shebang', 'yup');
		$this->assertEquals('yup', $this->_container->offsetGet('shebang'));
	}

	/**
	 * @covers \SiTech\Helper\Container::offsetUnset
	 */
	public function testOffsetUnset()
	{
		$this->_container->offsetSet('unset', 'yup');
		$this->_container->offsetUnset('unset');
		$this->assertEmpty($this->_container->offsetGet('unset'));
	}

	/**
	 * @covers \SiTech\Helper\Container::get
	 */
	public function testGet()
	{
		$this->assertEquals('bar', $this->_container->get('foo'));
		$this->assertNull($this->_container->get('isnotset'));
		$this->assertEquals('baz', $this->_container->get('isnotset', 'baz'));
	}

	/**
	 * @covers \SiTech\Helper\Container::__get
	 */
	public function testMagicGet()
	{
		$this->assertEquals('bar', $this->_container->foo);
		$this->assertNull($this->_container->isnotset);
	}

	/**
	 * @covers \SiTech\Helper\Container::__set
	 */
	public function testMagicSet()
	{
		$this->_container->bar = 'baz';
		$this->assertEquals('baz', $this->readAttribute($this->_container, 'attributes')['bar']);
	}

	/**
	 * @covers \SiTech\Helper\Container::__isset
	 */
	public function testMagicIsset()
	{
		$this->assertTrue(isset($this->_container->foo));
		$this->assertFalse(isset($this->_container->iamnotset));
	}
}

class Container {
	use \SiTech\Helper\Container;
}