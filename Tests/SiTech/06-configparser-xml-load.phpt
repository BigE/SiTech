--TEST--
SiTech_ConfigParser(); Load configuration with the XML file format.
--SKIPIF--
<?php
require_once('SiTech_Test.php');
if (!file_exists(SITECH_BASEPATH.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR.'test-config.ini')) die('skip the file test-config.ini does not exist.');
?>
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/ConfigParser.php');
require_once('SiTech/ConfigParser/Handler/XML.php');
try {
	$config = SiTech_ConfigParser::load(array(SiTech_ConfigParser::ATTR_HANDLER => new SiTech_ConfigParser_Handler_XML));
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

$return = $config->read(array(SITECH_BASEPATH.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR.'test-config.xml'));
if ($return[SITECH_BASEPATH.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR.'test-config.xml'] !== true) {
	var_dump($return);
} else {
	var_dump($config->get('foo', 'bar'));
}
?>

--EXPECT--
string(3) "baz"