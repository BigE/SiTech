--TEST--
SiTech_Exception base usage.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Plugins.php');

$plugins = new SiTech_Plugins();
$plugins->load('Foo', '../Content/plugin.Foo.php');
?>
--EXPECT--
foo