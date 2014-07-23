<?php
namespace Database {
	/**
	 * @group Database
	 */
	class DatabaseGrammarTest extends \PHPUnit_Framework_TestCase
	{
		/**
		 * @var MockGrammar
		 */
		protected $grammar;

		public function setUp()
		{
			$this->grammar = new MockGrammar();
		}

		/**
		 * @covers \SiTech\Database\Grammar\Grammar::wrap
		 * @covers \SiTech\Database\Grammar\Grammar::wrapValue
		 */
		public function testWrap()
		{
			$this->assertEquals('"foobar"', $this->grammar->wrap('foobar'));
			$this->assertEquals('"foo"."bar"', $this->grammar->wrap('foo.bar'));
			$this->assertEquals('"foobar" as "f"', $this->grammar->wrap('foobar as f'));
			$this->assertEquals('*', $this->grammar->wrap('*'));
		}

		/**
		 * @covers \SiTech\Database\Grammar\Grammar::getTablePrefix
		 * @covers \SiTech\Database\Grammar\Grammar::setTablePrefix
		 * @covers \SiTech\Database\Grammar\Grammar::prefixTable
		 */
		public function testTablePrefix()
		{
			$this->grammar->setTablePrefix('prefix_');
			$this->assertEquals('prefix_', $this->grammar->getTablePrefix());
			$this->assertEquals('"prefix_table"."field"', $this->grammar->wrap('table.field'));
		}
	}

	class MockGrammar extends \SiTech\Database\Grammar\Grammar
	{
	}
}