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
		foreach ($extensions as $ext) {
			$this->_allowed[$ext] = $handler;
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
