--TEST--
SiTech_ConfigParser(); Save configuration with the INI file format.
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
$return = $config->write(SITECH_BASEPATH.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR.'test-config.ini');
if ($return === true) {
	var_dump('Wrote file');
} else {
	var_dump($return);
}
?>

--EXPECT--
string(10) "Wrote file"