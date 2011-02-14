--TEST--
SiTech_Template::_handleError(); - Make sure templates respect the error mode settings.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Template.php');

$template = new SiTech\Template();
try {
    $template->setAttribute(SiTech\Template::ATTR_ERRMODE, SiTech\Template::ERRMODE_NONE);
    $template->display('non-existant.tpl');
    $template->setAttribute(SiTech\Template::ATTR_ERRMODE, SiTech\Template::ERRMODE_WARNING);
    $template->display('non-existant.tpl');
    $template->setAttribute(SiTech\Template::ATTR_ERRMODE, SiTech\Template::ERRMODE_EXCEPTION);
    $template->display('non-existant.tpl');
} catch (Exception $e) {
    var_dump($e->getMessage());
}
?>
--EXPECTF--
Warning: Unable to read file non-existant.tpl on path  in %s on line %i
string(45) "Unable to read file non-existant.tpl on path "