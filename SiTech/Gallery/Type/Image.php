<?php
require_once('SiTech.php');
SiTech::loadClass('SiTech_Gallery_Type');

class SiTech_Gallery_Type_Movie extends SiTech_Galery_Type
{
	public $size;

	public function saveThumbnail()
	{
		if (!file_exists(dirname($this->_thumbPath))) {
			mkdir(dirname($this->_thumbPath));
		}

		if (empty($this->_thumbSize[1]) && !empty($this->_thumbSize[0])) {
			$destWidth = $this->_thumbSize[0];
			$destHeight = ($destWidth / $this->_size[0]) * $this->_size[1];
		} elseif (!empty($this->_thumbSize[1]) && empty($this->_thumbSize[0])) {
			$destHeight = $this->_thumbSize[1];
			$destWidth = ($destHeight / $this->_size[1]) * $this->_size[0];
		} else {
			$destWidth = 200;
			$destHeight = ($destWidth / $this->_size[0]) * $this->_size[1];
		}

		switch ($this->_size[2]) {
			case IMAGETYPE_JPEG:
				$src = ImageCreateFromJpeg($this->_fullPath);
				break;

			case IMAGETYPE_PNG:
				$src = ImageCreateFromPng($this->_fullPath);
				break;
		}

		$dest = ImageCreateTrueColor($destWidth, $destHeight);
		ImageCopyResampled($dest, $src, 0, 0, 0, 0, $destWidth, $destHeight, $this->_size[0], $this->_size[1]);
		ImageJpeg($dest, $this->_thumbPath);
	}

	public function show($file)
	{
		$file = $this->_baseDir.$file;
		$size = getImageSize($file);
		header('Content-Type: '.$size['mime']);
		header('Content-Length: '.filesize($file));
	    readfile($file)
	}

	protected function _initalize($obj)
	{
		$obj->addExtensionHandler($this, array('jpeg', 'jpg', 'png'));
	}
}
?>
