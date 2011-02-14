--TEST--
SiTech_Exception base usage.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Exception.php');

$errstr = '#%d This is my %s test string';
try {
	throw new SiTech\Exception($errstr, array(1, 'beautiful'));
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

try {
	throw new SiTech\Exception($errstr, array(2, 'horrible'));
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

try {
	throw new SiTech\Exception($errstr, array(3, 'final'));
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}
?>

--EXPECT--
#1 This is my beautiful test string
#2 This is my horrible test string
#3 This is my final test string