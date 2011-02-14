--TEST--
SiTech_ConfigParser(); Save configuration with the Array file format.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/ConfigParser.php');
require_once('SiTech/ConfigParser/Handler/Array.php');
try {
	$config = SiTech\ConfigParser::load(array(SiTech\ConfigParser::ATTR_HANDLER => new SiTech\ConfigParser\Handler\_Array));
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

$config->addSection('foo');
$config->set('foo', 'bar', 'baz');
$config->set('foo', 'baz', array('foo', 'bar', array('baz', 'shebang')));
$config->set('foo', 'hmm', array('whee\'' => 'uNF', 123));
$config->set('foo', 'myobj', new stdclass());
$return = $config->write(dirname(__FILE__).'/test-config.php');
if ($return === true) {
	var_dump('Wrote file');
} else {
	var_dump($return);
}
?>

--EXPECT--
string(10) "Wrote file"