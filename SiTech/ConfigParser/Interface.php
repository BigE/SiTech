<?php
interface SiTech_ConfigParser_Interface
{
    public function addSection($section);
    public function defaults();
    public function get($section, $option);
    public function getInt($section, $option);
    public function getFloat($section, $option);
    public function getBool($section, $option);
    public function hasSection($section);
    public function items($section);
    public function options($section);
    public function read($file);
    public function removeOption($section, $option);
    public function removeSection($section);
    public function sections();
    public function set($section, $option, $value);
    public function write($file);
}
?>
