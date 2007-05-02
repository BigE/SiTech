<?php
require_once('SiTech.php');
SiTech::loadInterface('SiTech_Gallery_Type_Interface');

abstract class SiTech_Gallery_Type implements SiTech_Gallery_Type_Interface
{
	protected $_baseDir;

	protected $_file;

	public function __construct($baseDir, $file, $thumbSize)
	{
		$this->_baseDir = $baseDir;
		$this->_file = $file;
		$this->_fullPath = realpath($baseDir.PATH_SEPERATOR.$file);
		$this->_thumbPath = realpath($this->_baseDir.PATH_SEPERATOR.'thumbs'.PATH_SEPERATOR.$file);
		$this->_thumbSize = $thumbSize;

		$this->_initalize();
	}

	public function showThumbnail()
	{
		if (!file_exists($this->_thumbPath)) {
			$this->saveThumbnail();
		}

		$size = getimagesize($this->_thumbPath);
		header('Content-Type: '.$size['mime']);
		header('Content-Length: '.filesize($this->_thumbPath));
		readfile($this->_thumbPath);
	}
}
?>
