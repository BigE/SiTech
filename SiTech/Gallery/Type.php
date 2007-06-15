<?php
require_once('SiTech.php');
SiTech::loadInterface('SiTech_Gallery_Type_Interface');

abstract class SiTech_Gallery_Type implements SiTech_Gallery_Type_Interface
{
	protected $_baseDir;

	protected $_file;

	public function __construct($baseDir, $thumbSize, $obj)
	{
		$this->_baseDir = $baseDir;
		$this->_thumbSize = $thumbSize;

		$this->_initalize($obj);
	}

	public function showThumbnail($file)
	{
		$thumbFile = $this->_baseDir.$file;
		if (!file_exists($thumbFile)) {
			$this->saveThumbnail($file);
		}

		$size = getimagesize($thumbFile);
		header('Content-Type: '.$size['mime']);
		header('Content-Length: '.filesize($thumbFile));
		readfile($thumbFile);
	}
}
?>
