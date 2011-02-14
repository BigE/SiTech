--TEST--
SiTech_ConfigParser(); Load configuration with the Array file format.
--SKIPIF--
<?php
require_once('SiTech_Test.php');
if (!file_exists(dirname(__FILE__).'/test-config.php')) die('skip the file test-config.php does not exist.');
?>
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

$return = $config->read(array(dirname(__FILE__).'/test-config.php'));
if ($return[dirname(__FILE__).'/test-config.php'] !== true) {
	var_dump($return);
} else {
	var_dump($config->get('foo', 'bar'));
	var_dump($config->get('foo', 'baz'));
	var_dump($config->get('foo', 'hmm'));
	var_dump($config->get('foo', 'myobj'));
}
?>
--CLEAN--
<?php
require_once('SiTech_Test.php');
$file = dirname(__FILE__).'/test-config.php';
if (file_exists($file)) unlink($file);
?>
--EXPECT--
string(3) "baz"
array(3) {
  [0]=>
  string(3) "foo"
  [1]=>
  string(3) "bar"
  [2]=>
  array(2) {
    [0]=>
    string(3) "baz"
    [1]=>
    string(7) "shebang"
  }
}
array(2) {
  ["whee'"]=>
  string(3) "uNF"
  [0]=>
  int(123)
}
object(stdClass)#3 (0) {
}