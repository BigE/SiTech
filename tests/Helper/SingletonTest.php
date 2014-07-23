<?php
namespace Helper
{
	/**
	 * @group Helper
	 */
	class SingletonTest extends \PHPUnit_Framework_TestCase
	{
		/**
		 * @covers \SiTech\Helper\Singleton::getInstance
		 */
		public function testGetInstance()
		{
			$this->assertNull($this->readAttribute('\Helper\Singleton', 'instance'));
			$this->assertInstanceOf('\Helper\Singleton', ($s1 = Singleton::getInstance()));
			$this->assertInstanceOf('\Helper\Singleton', $this->readAttribute('\Helper\Singleton', 'instance'));
			$this->assertEquals('bar', $s1->foo);
			$s1->foo = 'baz';
			$this->assertInstanceOf('\Helper\Singleton', ($s2 = Singleton::getInstance()));
			$this->assertEquals('baz', $s2->foo);
		}
	}

	class Singleton
	{
		use \SiTech\Helper\Singleton;
		public $foo = 'bar';
	}
}