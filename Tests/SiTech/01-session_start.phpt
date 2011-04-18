--TEST--
SiTech_Session::start(); Basic usage.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Session.php');
try {
	SiTech\Session::start();
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

$_SESSION->setAttribute(SiTech\Session::ATTR_SESSION_NAME, 10);
var_dump('Hello');
$_SESSION->setAttribute(SiTech\Session::ATTR_STRICT, true);
?>

--EXPECT--
string(5) "Hello"
