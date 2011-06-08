--TEST--
SiTech_Session::start(); usage with database handler.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Session/Base.php');
require_once('SiTech/DB/Engine.php');
require_once('SiTech/DB/Driver/SQLite.php');
require_once('SiTech/Session/Handler/DB.php');
try {
	$db = new SiTech\DB\Engine(array('dsn' => 'sqlite::memory:'), SiTech\DB\DRIVER\SQLITE);
	$db->exec(file_get_contents(dirname(__FILE__).'/../../Tools/SiTech_Session.sql'));
	$handler = new SiTech\Session\Handler\DB($db, 'SiTech_Sessions');
	SiTech\Session\Base::start($handler);
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

$_SESSION->setAttribute(SiTech\Session\ATTR_SESSION_NAME, 10);
var_dump('Hello');
$_SESSION->setAttribute(SiTech\Session\ATTR_STRICT, true);
?>

--EXPECT--
string(5) "Hello"
