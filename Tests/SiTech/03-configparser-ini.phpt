--TEST--
SiTech_Session::start(); usage with database handler.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/ConfigParser.php');
try {
	$config = SiTech_ConfigParser::load();
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

$config->addSection('foo');
$config->set('foo', 'bar', 'baz');
var_dump('Hello');
?>

--EXPECT--
string(5) "Hello"
