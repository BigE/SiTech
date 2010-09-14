--TEST--
SiTech_Loader::loadModel() usage
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Loader.php');

define('SITECH_APP_PATH', realpath('../Content'));
SiTech_Loader::loadModel('Test');
$model = new TestModel();
?>
--EXPECT--
foo