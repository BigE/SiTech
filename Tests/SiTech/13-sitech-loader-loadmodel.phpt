--TEST--
SiTech_Loader::loadModel() usage
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Loader.php');

define('SITECH_APP_PATH', realpath(dirname(__FILE__).'/../Content'));
SiTech_Loader::registerAutoload();
SiTech_Loader::loadModel('Test');
$model = new TestModel();
var_dump($model);
?>
--EXPECT--
object(TestModel)#1 (4) {
  ["errors"]=>
  array(0) {
  }
  ["_fields":protected]=>
  array(0) {
  }
  ["_hasMany":protected]=>
  array(0) {
  }
  ["_hasOne":protected]=>
  array(0) {
  }
}
