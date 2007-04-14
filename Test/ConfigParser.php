<?php
set_include_path(get_include_path().PATH_SEPARATOR.realpath('../'));
require_once('SiTech.php');
SiTech::loadClass('Test_Abstract');

class Test_ConfigParser extends Test_Abstract
{
	public function __construct($class, $ext)
	{
		SiTech::loadClass($class);
		$this->_obj = new $class();
		$this->testMethod('read', array('./ConfigParser/Read.'.$ext));
		$this->testMethod('addSection', array('MySection'));
		$this->testMethod('set', array('MySection', 'test', 'Test Successful!'));
		$this->testMethod('set', array('MySection', 'myArray', array('foo', 'bar', array('baz', 'bash'))));
		$this->testMethod('write', array('./ConfigParser/Write.'.$ext));
	}
}

echo 'Testing class SiTech_ConfigParser_INI',"\n";
$obj = new Test_ConfigParser('SiTech_ConfigParser_INI', 'ini');

echo 'Testing class SiTech_ConfigParser_XML',"\n";
$obj = new Test_ConfigParser('SiTech_ConfigParser_XML', 'xml');
?>
