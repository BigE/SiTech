--TEST--
SiTech_Session::start(); usage with database handler.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Session.php');
require_once('SiTech/DB.php');
require_once('SiTech/Session/Handler/DB.php');
try {
	$db = new SiTech_DB(array('dsn' => 'sqlite::memory:'), SiTech_DB::DRIVER_SQLITE);
	$db->exec(file_get_contents(dirname(__FILE__).'/../../Tools/SiTech_Session.sql'));
	$handler = new SiTech_Session_Handler_DB($db, 'SiTech_Sessions');
	SiTech_Session::registerHandler($handler);
	SiTech_Session::start();
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

$_SESSION->setAttribute(SiTech_Session::ATTR_SESSION_NAME, 10);
var_dump('Hello');
$_SESSION->setAttribute(SiTech_Session::ATTR_STRICT, true);
?>

--EXPECT--
string(5) "Hello"
