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

		switch ($srcInfo[2]) {
			case 2:
				$src = ImageCreateFromJpeg($this->_fullPath);
				break;

			case 3:
				$src = ImageCreateFromPng($this->_fullPath);
				break;
		}

		$dest = ImageCreateTrueColor($destWidth, $destHeight);
		ImageCopyResampled($dest, $src, 0, 0, 0, 0, $destWidth, $destHeight, $srcInfo[0], $srcInfo[1]);
		ImageJpeg($dest, $this->_thumbPath);
	}

	public function show()
	{
	}

	protected function _initalize()
	{
		$this->size = getimagesize($this->_fullPath);
	}
}
?>
