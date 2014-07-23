<?php
namespace Config
{
	/**
	 * @group Config
	 */
	class RegistryTest extends \PHPUnit_Framework_TestCase
	{
		/**
		 * @covers \SiTech\Config\Registry::hasKey
		 */
		public function testHasKey()
		{
			$c = new \SiTech\Config\Registry();
			$this->assertFalse($c->hasKey('does.not.exist'));
		}

		/**
		 * @covers \SiTech\Config\Registry::set
		 * @covers \SiTech\Config\Registry\DuplicateKey
		 */
		public function testSet()
		{
			$config = new \SiTech\Config\Registry();
			$config->set('foo', 'bar');
			$this->assertEquals('bar', $config->get('foo'));
			$config->set('foo', 'baz');
			$this->assertEquals('baz', $config->get('foo'));
			$this->setExpectedException('\SiTech\Config\Registry\DuplicateKey', 'The key foo already exists in the configuration');
			$config->set('foo', 'duplicate key with strict mode', true);
		}

		/**
		 * @covers \SiTech\Config\Registry::get
		 * @covers \SiTech\Config\Registry\MissingKey
		 */
		public function testGet()
		{
			$c = new \SiTech\Config\Registry();
			$c->set('my.existing.key', 'whee');
			$this->assertEquals('whee', $c->get('my.existing.key'));
			$this->setExpectedException('\SiTech\Config\Registry\MissingKey', 'The key my.missing.key is not currently present in the configuration');
			$c->get('my.missing.key');
		}

		/**
		 * @covers \SiTech\Config\Registry::getBoolean
		 */
		public function testGetBoolean()
		{
			$c = new \SiTech\Config\Registry();
			$c->set('bool.test.true', true);
			$this->assertTrue($c->getBoolean('bool.test.true'));
			$c->set('bool.test.false', false);
			$this->assertFalse($c->getBoolean('bool.test.false'));
			// for true coverage
			$c->set('bool.test.string.true', 'true');
			$this->assertTrue($c->getBoolean('bool.test.string.true'));
			$c->set('bool.test.string.yes', 'yes');
			$this->assertTrue($c->getBoolean('bool.test.string.yes'));
			$c->set('bool.test.string.on', 'on');
			$this->assertTrue($c->getBoolean('bool.test.string.on'));
			$c->set('bool.test.string.1', '1');
			$this->assertTrue($c->getBoolean('bool.test.string.1'));
			$c->set('bool.test.int.1', 1);
			$this->assertTrue($c->getBoolean('bool.test.int.1'));
		}

		/**
		 * @covers \SiTech\Config\Registry::getFloat
		 */
		public function testGetFloat()
		{
			$c = new \SiTech\Config\Registry();
			$c->set('float.test.pi', 3.14159);
			$this->assertEquals(3.14159, $c->getFloat('float.test.pi'));
			$c->set('float.test.string', 'bar');
			$this->assertEquals(0, $c->getInteger('float.test.string'));
		}

		/**
		 * @covers \SiTech\Config\Registry::getInteger
		 */
		public function testGetInteger()
		{
			$c = new \SiTech\Config\Registry();
			$c->set('integer.test.integer', 1);
			$this->assertEquals(1, $c->getInteger('integer.test.integer'));
			$c->set('integer.test.string', 'bar');
			$this->assertEquals(0, $c->getInteger('integer.test.string'));
			$c->set('integer.test.float', 3.14159);
			$this->assertEquals(3, $c->getInteger('integer.test.float'));
		}
	}
}
 