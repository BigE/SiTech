<?php

class DatabaseConnectionTest extends PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$conn = new MockConnection(['dsn' => 'sqlite::memory:']);
		$this->assertInstanceOf('\SiTech\Database\Connection', $conn);
	}
}

class MockConnection extends \SiTech\Database\Connection
{
	protected function _generateDsn(array $config)
	{
		// TODO: Implement _generateDsn() method.
	}
}
