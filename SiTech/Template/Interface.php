<?php
interface SiTech_Template_Interface
{
	public function __construct($file, $path=null);
	public function assign($variable, $value);
	public function display();
	public function render();
	public function setTemplatePath($path);
	public function unassign($variable);
}
?>