<?php
require_once('SiTech.php');
SiTech::loadClass('SiTech_Gallery_Type');

class SiTech_Gallery_Type_Movie extends SiTech_Galery_Type
{
	public $movie;

	public function saveThumbnail()
	{
		$frames = $this->movie->getFrameCount();
		$subFrames = $frames / 4;

		$frame = array();
		for ($i = 1; $i <= 4; $i++) {
			$frame = $this->movie->getFrameNumber(mt_rand(floor(($i - 1) * $subFrames), floor($i * $subFrames)));
			
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

			$frame->resize($destWidth, $destHeight);
			$gdThumb = $frame->toGDImage();
			ImageJpeg($gdThumb, $this->thumbPath.'.'.$i.'.jpg');
		}
	}

	public function show()
	{
	}

	protected function _initalize()
	{
		if (!extension_loaded('ffmpeg-php') && !@dl('ffmpeg-php.'.((substr(PHP_OS, 0, 3) == 'WIN')? 'dll' : 'so'))) {
			SiTech::loadClass('SiTech_Gallery_Exception');
			throw new SiTech_Gallery_Exception('ffmpeg-php extension not loaded. Unable to parse movie files.');
		}

		$this->movie = new ffmpeg_movie($this->_baseDir.'/'.$this->movie, false);
	}
}
?>
