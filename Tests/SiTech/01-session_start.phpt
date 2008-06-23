--TEST--
SiTech_Session::start(); usage
--FILE--
<?php
require_once(dirname(__FILE__).'/../../lib/SiTech/Session.php');
try {
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
