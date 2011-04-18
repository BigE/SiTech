--TEST--
SiTech_ConfigParser(); Load configuration with the INI file format.
--SKIPIF--
<?php
require_once('SiTech_Test.php');
if (!file_exists(dirname(__FILE__).'/test-config.ini')) die('skip the file test-config.ini does not exist.');
?>
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/ConfigParser.php');
try {
	$config = SiTech_ConfigParser::load();
	$config->read(array(dirname(__FILE__).'/test-config.ini'));
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

var_dump($config->get('foo', 'bar'));
?>
--CLEAN--
<?php
require_once('SiTech_Test.php');
$file = dirname(__FILE__).'/test-config.ini';
if (file_exists($file)) unlink($file);
?>
--EXPECT--
string(3) "baz"
