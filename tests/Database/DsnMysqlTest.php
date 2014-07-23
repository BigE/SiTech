<?php
namespace Database {
	/**
	 * @group Database
	 */
	class DBDsnMySQLTest extends \PHPUnit_Framework_TestCase
	{
		/**
		 * @covers \SiTech\Database\Dsn\MySQL::_generateDsn
		 */
		public function testUnixSocket()
		{
			$dsn = new \SiTech\Database\Dsn\MySQL([
				'database'      => 'test_db',
				'unix_socket'   => '/var/run/mysqld/mysqld.sock'
			]);

			$this->assertEquals('mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=test_db', $dsn->getDsn());
		}

		/**
		 * @covers \SiTech\Database\Dsn\MySQL::_generateDsn
		 */
		public function testHostPort()
		{
			$dsn = new \SiTech\Database\Dsn\MySQL([
				'database'  => 'test_db',
				'host'      => 'localhost'
			]);

			$this->assertEquals('mysql:host=localhost;dbname=test_db', $dsn->getDsn());
			$dsn->offsetSet('port', 3306);
			$this->assertEquals('mysql:host=localhost;port=3306;dbname=test_db', $dsn->getDsn());
		}

		/**
		 * @covers \SiTech\Database\Dsn\MySQL::_generateDsn
		 */
		public function testCharset()
		{
			$dsn = new \SiTech\Database\Dsn\MySQL([
				'charset'   => 'utf8',
				'database'  => 'test_db',
				'host'      => 'localhost'
			]);

			$this->assertEquals('mysql:host=localhost;dbname=test_db;charset=utf8', $dsn->getDsn());
		}
	}
}