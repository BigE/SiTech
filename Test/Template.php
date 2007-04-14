<?php
require_once('Abstract.php');
SiTech::loadClass('SiTech_Template');

class Test_Template extends Test_Abstract
{
	public function __construct()
	{
		$this->_obj = new SiTech_Template('test.php');
		$this->testMethod('setTemplatePath', array(realpath('./Template')));
		$this->testMethod('assign', array('foo', 'This is my first variable.'));
		$this->testMethod('assign', array('title', 'Page Title'));
		$this->testMethod('assign', array('array', range('A', 'Z')));
		$this->testMethod('display', array());
	}
}

$test = new Test_Template();
?>
