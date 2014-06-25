<?php

class DatabaseConnectionTest extends PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$conn = new \SiTech\Database\Connection();
		$this->assertInstanceOf('\SiTech\Database\Connection', $conn);
	}
} 