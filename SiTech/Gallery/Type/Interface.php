<?php
interface SiTech_Gallery_Type_Interface
{
	public function __construct($baseDir, $file, $thumbSize);
	public function saveThumbnail();
	public function show();
	public function showThumbnail();
	protected function _initalize();
}
?>
