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
		 * @covers \SiTech\Config\Registry::removeSection
		 */
		public function testRemoveSection()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->addSection('remove_me');
			$this->assertTrue($c->removeSection('remove_me'));
			$this->assertArrayNotHasKey('remove_me', $this->readAttribute($c, 'registry'));
			$this->assertFalse($c->removeSection('missing_section'));
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
		 * @covers \SiTech\Config\Registry::hasOption
		 */
		public function testHasOption()
		{
			$c = new \SiTech\Config\Registry();
			$this->assertFalse($c->hasOption('missing_section', 'missing_option'));
			$c->addSection('phpunit');
			$this->assertFalse($c->hasOption('phpunit', 'missing_option'));
			$c->set('phpunit', 'option', 'value');
			$this->assertTrue($c->hasOption('phpunit', 'option'));
		}

		/**
		 * @covers \SiTech\Config\Registry::set
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
		 * @covers \SiTech\Config\Registry::set
		 * @covers \SiTech\Config\Registry\Exception\MissingSection
		 */
		public function testSetMissingSection()
		{
			$config = new \SiTech\Config\Registry();
			$this->setExpectedException('\SiTech\Config\Registry\Exception\MissingSection', 'The section missing_section is not currently present in the configuration');
			$config->set('missing_section', 'foo', 'bar');
		}

		/**
		 * @covers \SiTech\Config\Registry::removeOption
		 */
		public function testRemoveOption()
		{
			$c = new \SiTech\Config\Registry();
			$section = uniqid('phpunit', true);
			$option = uniqid('option', true);
			$value = uniqid('value', true);
			$c->addSection($section);
			$c->set($section, $option, $value);
			$this->assertTrue($c->removeOption($section, $option));
			$this->assertFalse($c->removeOption($section, uniqid('missing_option', true)));
		}

		/**
		 * @covers \SiTech\Config\Registry::removeOption
		 * @covers \SiTech\Config\Registry\Exception\MissingSection
		 */
		public function testRemoveOptionMissingSection()
		{
			$c = new \SiTech\Config\Registry();
			$section = uniqid('missing_section', true);
			$this->setExpectedException('\SiTech\Config\Registry\Exception\MissingSection', 'The section '.$section.' is not currently present in the configuration');
			$c->removeOption($section, uniqid('missing_option', true));
		}

		/**
		 * @covers \SiTech\Config\Registry::get
		 */
		public function testGet()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->set('phpunit', 'existing_key', 'whee');
			$this->assertEquals('whee', $c->get('phpunit', 'existing_key'));
			$this->assertEquals('default value', $c->get('phpunit', 'missing_key_default', false, [], 'default value'));
		}

		/**
		 * @covers \SiTech\Config\Registry::get
		 * @covers \SiTech\Config\Registry\Exception\MissingOption
		 */
		public function testGetMissingOption() {
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$this->setExpectedException('\SiTech\Config\Registry\Exception\MissingOption', 'The option missing_key is not currently set in the section phpunit of the configuration');
			$c->get('phpunit', 'missing_key');
		}

		/**
		 * @covers \SiTech\Config\Registry::get
		 * @covers \SiTech\Config\Registry\Exception\MissingSection
		 */
		public function testGetMissingSection() {
			$c = new \SiTech\Config\Registry();
			$this->setExpectedException('\SiTech\Config\Registry\Exception\MissingSection', 'The section missing_section is not currently present in the configuration');
			$c->get('missing_section', 'missing_key');
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
		 * @covers \SiTech\Config\Registry::items
		 */
		public function testItems()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->addSection('testSection');
			$c->set('phpunit', 'foo', 'bar');
			$c->set('phpunit', 'bar', 'baz');
			$c->set('testSection', 'baz', 'shebang');
			$c->set('testSection', 'shebang', 'foo');
			$expected = ['phpunit' => ['foo' => 'bar', 'bar' => 'baz'], 'testSection' => ['baz' =>'shebang', 'shebang' => 'foo']];
			$this->assertEquals($expected, $c->items());
			$this->assertEquals($expected['phpunit'], $c->items('phpunit'));
			$this->assertEquals($expected['testSection'], $c->items('testSection'));
		}

		/**
		 * @covers \SiTech\Config\Registry::items
		 * @covers \SiTech\Config\Registry\Exception\MissingSection
		 */
		public function testItemsMissingSection()
		{
			$c = new \SiTech\Config\Registry();
			$this->setExpectedException('\SiTech\Config\Registry\Exception\MissingSection', 'The section missing_section is not currently present in the configuration');
			$c->items('missing_section');
		}

		/**
		 * @covers \SiTech\Config\Registry::options
		 */
		public function testOptions()
		{
			$c = new \SiTech\Config\Registry();
			$c->addSection('phpunit');
			$c->addSection('testSection');
			$c->set('phpunit', 'foo', 'bar');
			$c->set('phpunit', 'bar', 'baz');
			$c->set('testSection', 'baz', 'shebang');
			$c->set('testSection', 'shebang', 'foo');
			$expected = ['phpunit' => ['foo' => 'bar', 'bar' => 'baz'], 'testSection' => ['baz' =>'shebang', 'shebang' => 'foo']];
			$this->assertEquals(array_keys($expected['phpunit']), $c->options('phpunit'));
			$this->assertEquals(array_keys($expected['testSection']), $c->options('testSection'));
		}

		/**
		 * @covers \SiTech\Config\Registry::options
		 * @covers \SiTech\Config\Registry\Exception\MissingSection
		 */
		public function testOptionsMissingSection()
		{
			$c = new \SiTech\Config\Registry();
			$section = uniqid('missing_section', true);
			$this->setExpectedException('\SiTech\Config\Registry\Exception\MissingSection', 'The section '.$section.' is not currently present in the configuration');
			$c->options($section);
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
 