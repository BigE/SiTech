<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author Eric
 */
abstract class SiTech_Syntax_Highlight_Abstract
{
	const DISALLOW_FILE = 1;

	const DISALLOW_DIR = 2;

	protected $_folders;
	
	protected $_source;

	public function __construct(array $folders = array())
	{
		$this->_folders = $folders;
	}

	public function disallow(array $location)
	{
		$this->_disallow = $location;
	}
	
	public function displaySource()
	{
		$source = $this->_parseSource();
		$lines = explode("<br />", $source);
		$numLines = sizeof($lines);
		$lineno = 1;
		$pad = 1;

		for ($i = 10; $i < 100000000000;) {
			if (($numLines / $i) > 1) {
				$pad++;
			} else {
				break;
			}

			$i = $i * 10;
		}

		echo '<div id="sitech_source">',"\n";
		foreach ($lines as $line) {
			echo '<span class="num"><code><a name="',$lineno,'"></a>',sprintf('%0'.$pad.'s', $lineno),'</code></span>';
			echo $line,'<br />',"\n";
			$lineno++;
		}
		echo '</div>',"\n";
	}

	public function isAllowed($file)
	{
		$found = false;
		foreach ($this->_folders as $folder) {
			if (substr($file, 0, strlen($folder)) == $folder) {
				$found = true;
			}
		}

		if ($found) {
			foreach ($this->_disallow as $disallow) {
				if ($disallow[0] == self::DISALLOW_DIR && substr($file, 0, strlen($disallow[1])) == $disallow[1]) {
					$found = false;
					break;
				} elseif ($disallow[0] == self::DISALLOW_FILE && basename($file) == $disallow[1]) {
					$found = false;
					break;
				}
			}
		}
		
		return($found);
	}

	public function loadFile($file)
	{
		$file = realpath($file);
		if ($file === false) {
			throw new Exception('Invalid file. 1');
		}

		
		$found = $this->isAllowed($file);

		if ($found) {
			$this->_source = file_get_contents($file);
			$this->_source = str_replace(array("\r\n", "\r"), "\n", $this->_source);
		} else {
			throw new Exception('Invalid file. 2');
		}
	}

	abstract protected function _parseSource();
}
