--TEST--
SiTech_Template::_handleError(); - Make sure templates respect the error mode settings.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Template.php');

$template = new SiTech_Template('non-existant.tpl');
try {
    $template->setAttribute(SiTech_Template::ATTR_ERRMODE, SiTech_Template::ERRMODE_NONE);
    $template->display();
    $template->setAttribute(SiTech_Template::ATTR_ERRMODE, SiTech_Template::ERRMODE_WARNING);
    $template->display();
    $template->setAttribute(SiTech_Template::ATTR_ERRMODE, SiTech_Template::ERRMODE_EXCEPTION);
    $template->display();
} catch (Exception $e) {
    var_dump($e->getMessage());
}
?>
--EXPECTF--
Warning: Unable to read file non-existant.tpl on path  in %s on line %i
string(45) "Unable to read file non-existant.tpl on path "