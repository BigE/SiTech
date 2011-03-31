--TEST--
SiTech_ConfigParser(); Save configuration with the XML file format.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/ConfigParser/RawConfigParser.php');
require_once('SiTech/ConfigParser/Handler/XML.php');
try {
	$config = new SiTech\ConfigParser\RawConfigParser(array(SiTech\ConfigParser\RawConfigParser::ATTR_HANDLER => new SiTech\ConfigParser\Handler\XML));
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

$config->addSection('foo');
$config->set('foo', 'bar', 'baz');
$return = $config->write(dirname(__FILE__).'/test-config.xml');
if ($return === true) {
	var_dump('Wrote file');
} else {
	var_dump($return);
}
?>

--EXPECT--
string(10) "Wrote file"
