<?php
namespace Database {
	/**
	 * @group Database
	 */
	class ConnectionTest extends \PHPUnit_Framework_TestCase
	{
		/**
		 * @covers \SiTech\Database\Connection\Connection::__construct
		 */
		public function testConstructor()
		{
			$conn = new MockConnection(['dsn' => 'sqlite::memory:']);
			$this->assertInstanceOf('\SiTech\Database\Connection\Connection', $conn);
		}
	}

	class MockConnection extends \SiTech\Database\Connection\Connection
	{
		protected function _generateDsn(array $config)
		{
			// TODO: Implement _generateDsn() method.
		}
	}
}