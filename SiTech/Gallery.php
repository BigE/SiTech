<?php
class SiTech_Gallery
{
	const TYPE_IMAGE = 'SiTech_Gallery_Type_Image';

	const TYPE_MOVIE = 'SiTech_Gallery_Type_Movie';

	private $_allowed = array();

	private $_dirAsThumb = false;

	private $_pageLimit = 20;

	private $_thumbnailHeight = null;

	private $_thumbnailWidth = 200;

	public function __construct($baseDir)
	{
	}

	public function __get($var)
	{
		switch ($var) {
			case 'dirAsThumb':
				return($this->_dirAsThumb);
				break;

			case 'pageLimit':
				return($this->_pageLimit);
				break;

			default:
				if (isset($this->_vars[$var])) {
					return($this->_vars[$var]);
				} else {
					return(null);
				}
		}
	}

	public function __set($var, $val)
	{
		switch ($var) {
			case 'dirAsThumb':
				$this->_dirAsThumb = (bool)$val;
				break;

			case 'pageLimit':
				$this->_pageLimit = (int)$val;
				break;

			default:
				$this->_vars[$var] = $val;
				break;
		}
	}

	public function addExtensionHandler($handler, $extensions)
	{
		if (is_array($extensions)) {
			foreach ($extensions as $ext) {
				$this->_allowed[$ext] = $handler;
			}
		} else {
			$this->_allowed[$ext] = $handler;
		}
	}

	public function getExt($file)
	{
		return(substr($file, strrpos($file, '.')));
	}

	public function loadHandler($type)
	{
		if (!isset($this->_obj[$type])) {
			SiTech::loadClass($type);
			$this->_obj[$type] = new $type($this->_dir, array($this->_thumbWidth, $this->_thumbHeight), $this);
		}
	}

	public function requireAuth($user, $pass) {
		if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != $user || !isset($_SERVER['PHP_AUTH_PW']) || sha1($_SERVER['PHP_AUTH_PW']) != sha1($pass)) {
			header('WWW-Authenticate: Basic realm="My Gallery"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'You are not authorized to view this gallery.';
			exit;
		}
	}

	public function handleRequest()
	{
		$ext = $this->getExt($_SERVER['PATH_INFO']);

		if (isset($this->_allowed[$ext])) {
			if (isset($_GET['thumb'])) {
				$this->_allowed[$ext]->showThumbnail($_SERVER['PATH_INFO']);
				exit;
			}
		} elseif (isset($_GET['dirThumb'])) {
			$dirThumb = $this->_dir.DIRECTORY_SEPERATOR.substr($_SERVER['PATH_INFO'], 1).DIRECTORY_SEPERATOR.'thumbs'.DIRECTORY_SEPERATOR.'gDirThumb.jpg';
			if (file_exists($dirThumb))	{
				$this->_allowed['jpg']->show($dirThumb);
			} else {
				/* get an array of all files */
				$dir = new DirectryIterator($this->_dir.DIRECTORY_SEPERATOR.substr($_SERVER['PATH_INFO'], 1));
				$files = array();
				foreach ($dir as $file) {
					$ext = $this->getExt($file->getFilename());
					if (!$file->isDir() && isset($this->_allowed[$ext)) {
						$files[] = $file;
					}
				}

				if (sizeof($files) > 0) {
					$dirThumbImg = ImageCreateTrueColor();
					for ($i = 0; $i < 4; $i++)  {
						$file = $files[mt_rand(0, sizeof($files))];
						$ext = $this->getExt($file);
						if (!file_exists($this->_dir.DIRECTORY_SEPERATOR.substr($_SERVER['PATH_INFO'], 1).DIRECTORY_SEPERATOR.'thumbs'.DIRECTORY_SEPERATOR.$file)) {
							$this->_allowed[$ext]->saveThumbnail(substr($_SERVER['PATH_INFO'], 1).DIRECTORY_SEPERATOR.$file);
						}

						$thumb = getimagesize($this->_dir.DIRECTORY_SEPERATOR.substr($_SERVER['PATH_INFO'], 1).DIRECTORY_SEPERATOR.'thumbs'.DIRECTORY_SEPERATOR.$file);
						swich ($i) {
							case 0:
								$dstX = 0;
								$dstY = 0;
								break;

							case 1:
								
								break;

							case 2:
								break;

							case 3:
								break;
						}
						$thumbImg = ImageCreateFromJpeg($this->_dir.DIRECTORY_SEPERATOR.substr($_SERVER['PATH_INFO'], 1).DIRECTORY_SEPERATOR.'thumbs'.DIRECTORY_SEPERATOR.$file);
						ImageCopyResampled($dirThumbImg, $thumbImg, $dstX, $dstY, 0, 0, $dstW, $dstH, $thumb[0], $thumb[1]);
					}
				} else {
				}
			}
			exit;
		}
	}

	public function showThumbs($start = 0, $limit = null)
	{
		if (empty($limit)) {
			$limit = $this->_pageLimit;
		}

		$dirItr = new DirectoryIterator($this->_dir);
		$i = 0;

		$dirs = array();
		$table = sprintf('<table>%s', "\n");
		foreach ($dirItr as $file) {
			if ($i < $start) {
				continue;
			}

			$fileExt = substr($file->getFilename(), strrpos($file->getFilename(), '.') + 1);

			if ($dirItr->isDot() || (!in_array($fileExt, $this->_allowed) && !$file->isDir())) {
				continue;
			}

			if ($i % 4 == 0) {
				$table .= sprintf('%s<tr>%s', "\t", "\n");
			}

			if ($file->isDir()) {
				if ($this->_dirAsThumb) {
					$table .= sprintf('<td><a href="%%s/%s"><img src="%%s/%s?dirThumb"></a></td>', $file->getFilename());
					$i++;
				} else {
					$dirs[] = $file->getFilename();
				}
			} else {
				$table .= sprintf('<td><a href="%%s/%s"><img src="%%s/%s?thumb" alt="%s" title="%s"></a></td>', $file->getFilename(), $file->getFilename(), $file->getFilename(), $file->getFilename());
				$i++;
			}

			if ($i % 4 == 0) {
				$table .= sprintf('%s</tr>%s', "\t", "\n");
			}

			if ($i == $this->_pageLimit) {
				break;
			}
		}
		$table .= sprintf('</table>%s', "\n");
	}
}
?>
