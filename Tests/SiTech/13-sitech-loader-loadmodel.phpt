--TEST--
SiTech\Loader::loadModel() usage
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Loader.php');

define('SITECH_APP_PATH', realpath(dirname(__FILE__).'/../Content'));
SiTech\Loader::registerAutoload();
SiTech\Loader::loadModel('Test');
TestModel::db(new SiTech\DB\Engine(array('dsn' => 'sqlite::memory:'), 'SiTech\DB\Driver\SQLite'));
$model = new TestModel();
var_dump($model);
?>
--EXPECT--
object(TestModel)#3 (6) {
  ["_belongsTo":protected]=>
  array(0) {
  }
  ["_db":protected]=>
  object(SiTech\DB\Engine)#1 (2) {
    ["driver":protected]=>
    object(SiTech\DB\Driver\SQLite)#2 (1) {
      ["pdo":protected]=>
      *RECURSION*
    }
    ["_queries":"SiTech\DB\Engine":private]=>
    array(0) {
    }
  }
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
