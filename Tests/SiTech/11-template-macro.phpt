--TEST--
SiTech_Exception base usage.
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/Template.php');
require_once('SiTech/Template/Renderer/Macro.php');
error_reporting(E_ALL);

$template = new SiTech_Template(SITECH_BASEPATH.'/Tests/Content');
$template->setAttribute(SiTech_Template::ATTR_RENDER_ENGINE, new SiTech_Template_Renderer_Macro());
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

Notice: Undefined variable: test in template %s/Tests/Content/macro.tpl code on line ?? in %s/SiTech/Template/Renderer/Macro.php on line %d
This is my  page for Macro templates.