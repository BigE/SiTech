--TEST--
SiTech_Exception base usage.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Template/Engine.php');
require_once('SiTech/Template/Renderer/Macro.php');
error_reporting(E_ALL);

$template = new SiTech\Template\Engine(SITECH_BASEPATH.'/Tests/Content');
$template->setAttribute(SiTech\Template\Engine::ATTR_RENDER_ENGINE, new SiTech\Template\Renderer\Macro());
$template->assign('test', 'awesome');
$template->display('macro.tpl');
$template->assign('test', 'horrible');
$template->display('macro.tpl');
$template->unassign('test');
$template->display('macro.tpl');
?>
--EXPECTF--
This is my awesome page for Macro templates.
This is my horrible page for Macro templates.
This is my  page for Macro templates.