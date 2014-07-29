<?php
namespace Config
{
	/**
	 * @group Config
	 */
	class RegistryTest extends \PHPUnit_Framework_TestCase
	{
		/**
		 * @covers \SiTech\Config\Registry::addSection
		 * @covers \SiTech\Config\Registry\Exception\DuplicateSection
		 */
		public function testAddSection()
		{
			$c = new \SiTech\Config\Registry();
			$this->assertEquals($c, $c->addSection('phpunit'));
			$this->assertArrayHasKey('phpunit', $this->readAttribute($c, 'registry'));
			$this->setExpectedException('\SiTech\Config\Registry\Exception\DuplicateSection', 'The section phpunit already exists in the configuration');
			$c->addSection('phpunit');
		}

		/**
		 * @covers \SiTech\Config\Registry::hasSection
		 */
		public function testHasSection()
		{
			$c = new \SiTech\Config\Registry();
			$this->assertFalse($c->hasSection('does.not.exist'));
			$c->addSection('does.exist');
			$this->assertTrue($c->hasSection('does.exist'));
		}

		/**
		 * @covers \SiTech\Config\Registry::set
		 * @covers \SiTech\Config\Registry\Exception\DuplicateSection
		 */
		public function testSet()
		{
			$config = new \SiTech\Config\Registry();
			$config->addSection('section');
			$config->set('section', 'foo', 'bar');
			$this->assertEquals('bar', $config->get('section', 'foo'));
			$config->set('section', 'foo', 'baz');
			$this->assertEquals('baz', $config->get('section', 'foo'));
		}

		/**
		 * @covers \SiTech\Config\Registry::get
		 * @covers \SiTech\Config\Registry\Exception\MissingOption
		 */
		public function testGet()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->set('phpunit', 'existing_key', 'whee');
			$this->assertEquals('whee', $c->get('phpunit', 'existing_key'));
			$this->setExpectedException('\SiTech\Config\Registry\Exception\MissingOption', 'The option missing_key is not currently set in the section phpunit of the configuration');
			$c->get('phpunit', 'missing_key');
		}

		/**
		 * @covers \SiTech\Config\Registry::getBoolean
		 * @covers \SiTech\Config\Registry\Exception\UnexpectedValue
		 */
		public function testGetBoolean()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->set('phpunit', 'bool.test.true', true);
			$this->assertTrue($c->getBoolean('phpunit', 'bool.test.true'));
			$c->set('phpunit', 'bool.test.false', false);
			$this->assertFalse($c->getBoolean('phpunit', 'bool.test.false'));
			// for true coverage
			$c->set('phpunit', 'bool.test.string.true', 'true');
			$this->assertTrue($c->getBoolean('phpunit', 'bool.test.string.true'));
			$c->set('phpunit', 'bool.test.string.yes', 'yes');
			$this->assertTrue($c->getBoolean('phpunit', 'bool.test.string.yes'));
			$c->set('phpunit', 'bool.test.string.on', 'on');
			$this->assertTrue($c->getBoolean('phpunit', 'bool.test.string.on'));
			$c->set('phpunit', 'bool.test.string.1', '1');
			$this->assertTrue($c->getBoolean('phpunit', 'bool.test.string.1'));
			$c->set('phpunit', 'bool.test.int.1', 1);
			$this->assertTrue($c->getBoolean('phpunit', 'bool.test.int.1'));
			// for false coverage
			$c->set('phpunit', 'bool.test.string.false', 'false');
			$this->assertFalse($c->getBoolean('phpunit', 'bool.test.string.false'));
			$c->set('phpunit', 'bool.test.string.no', 'no');
			$this->assertFalse($c->getBoolean('phpunit', 'bool.test.string.no'));
			$c->set('phpunit', 'bool.test.string.off', 'off');
			$this->assertFalse($c->getBoolean('phpunit', 'bool.test.string.off'));
			$c->set('phpunit', 'bool.test.string.0', '0');
			$this->assertFalse($c->getBoolean('phpunit', 'bool.test.string.0'));
			$c->set('phpunit', 'bool.test.int.0', 0);
			$this->assertFalse($c->getBoolean('phpunit', 'bool.test.int.0'));
			// stop... exception time
			$this->setExpectedException('\SiTech\Config\Registry\Exception\UnexpectedValue', 'Expecting boolean value, got this is not what it wants');
			$c->set('phpunit', 'bool.test.unexpectedvalue', 'this is not what it wants');
			$c->getBoolean('phpunit', 'bool.test.unexpectedvalue');
		}

		/**
		 * @covers \SiTech\Config\Registry::getFloat
		 */
		public function testGetFloat()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->set('phpunit', 'float.test.pi', 3.14159);
			$this->assertEquals(3.14159, $c->getFloat('phpunit', 'float.test.pi'));
			$c->set('phpunit', 'float.test.string', 'bar');
			$this->assertEquals(0, $c->getFloat('phpunit', 'float.test.string'));
		}

		/**
		 * @covers \SiTech\Config\Registry::getInt
		 */
		public function testGetInteger()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->set('phpunit', 'integer.test.integer', 1);
			$this->assertEquals(1, $c->getInt('phpunit', 'integer.test.integer'));
			$c->set('phpunit', 'integer.test.string', 'bar');
			$this->assertEquals(0, $c->getInt('phpunit', 'integer.test.string'));
			$c->set('phpunit', 'integer.test.float', 3.14159);
			$this->assertEquals(3, $c->getInt('phpunit', 'integer.test.float'));
		}

		/**
		 * @covers \SiTech\Config\Registry::sections
		 */
		public function testSections()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->addSection('foo');
			$c->addSection('bar');
			$this->assertEquals(['phpunit', 'foo', 'bar'], $c->sections());
		}
	}
}
 