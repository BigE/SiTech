<?php
/**
 * SiTech/Syntax/Highlight/Abstract.php
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2009
 * @filesource
 * @package SiTech_Syntax
 * @subpackage SiTech_Syntax_Highlight
 * @todo Finish file documentation.
 * @version $Id$
 */

/**
 * Description of Abstract
 *
 * @package SiTech_Syntax
 * @subpackage SiTech_Syntax_Highlight
 */
abstract class SiTech_Syntax_Highlight_Abstract
{
	const DISALLOW_FILE = 1;

	const DISALLOW_DIR = 2;

	const CASE_UPPER = 1;

	const CASE_LOWER = 2;

	const CASE_ANY = 3;

	const HIGHLIGHT_SCRIPT = 1;

	const HIGHLIGHT_ALL = 2;

	protected $_folders;

	protected $_rules = array();
	
	protected $_source;

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
		echo '<div style="float: left;" id="num">';
		foreach ($lines as $line) {
			echo '<span class="num"><a name="',$lineno,'"></a>',sprintf('%0'.$pad.'s', $lineno),'</span><br />',"\n";
			$lineno++;
		}
		echo '</div>',"\n";
		echo '<div id="code">',implode("<br />\n", $lines),'</div>',"\n";
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

		
		$found = true; //$this->isAllowed($file);

		if ($found) {
			$this->_source = file_get_contents($file);
			$this->_source = str_replace(array("\r\n", "\r"), "\n", htmlentities($this->_source));
			$this->_source = str_replace("\t", str_repeat('&nbsp;', $this->_rules['TAB_STOP']), $this->_source);
			$this->_source = str_replace(array('&quot;', ' '), array('"', '&nbsp;'), $this->_source);
		} else {
			throw new Exception('Invalid file. 2');
		}
	}

	protected function _parseSource()
	{
		/* initalize some variables */
		$matches = array();
		$lastMatchPos = 0;
		$length = strlen($this->_source);
		
		if ($this->_rules['HIGHLIGHT_MODE'] == self::HIGHLIGHT_SCRIPT) {
			/* We only need to parse specific parts inside tags */
			foreach ($this->_rules['SCRIPT_TOGGLE'] as $kScriptToggle => $tags) {
				if (is_array($tags)) {
					foreach ($tags as $open => $close) {
						$open = htmlentities($open);
						$close = htmlentities($close);
						/* Find the start of a tag, then make sure we haven't already matched it. */
						if (($startPos = strpos($this->_source, $open, $lastMatchPos)) !== false && !isset($matches[$startPos])) {
							$lastMatchPos = $startPos;
							if (($endPos = strpos($this->_source, $close, $startPos + strlen($open))) === false) {
								$endPos = $length;
							}

							$matches[$startPos] = array(
								'code' => substr($this->_source, $startPos, $endPos),
								'start' => $startPos,
								'end' => $endPos
							);
							
							/* No more to match */
							if ($endPos >= $length) break(2);
						}
					}
				} else {
					/* regex string */
					if (preg_match_all($tags, $this->_source, $matches, PREG_OFFSET_CAPTURE, $lastMatchPos)) {
					}
				}
			}
		} else {
			/* The whole file should be highlighted */
			$matches[0] = array(
				'code' => $this->_source,
				'start' => 0,
				'end' => $length
			);
		}

		foreach ($matches as $kMatch => &$match) {
			$regexMatches = array();
			foreach ($this->_rules['REGEXES'] as $regexKey => $regex) {
				if (preg_match_all($regex, $match['code'], $regexMatches[$regexKey])) {
					foreach ($regexMatches[$regexKey][0] as $key => $val) {
						$match['code'] = str_replace($val, '<STRE:'.$regexKey.':'.$key.'>', $match['code']);
					}
				}
			}

			foreach ($this->_rules['KEYWORDS'] as $wordsKey => $words) {
				array_walk($words, array($this, '_pregQuote'));
					$set = implode('|',$words);
					$sets = $this->_splitKeywordSet($set);

					for ($x = 0; $x < sizeof($sets); $x++) {
						if (preg_match_all('#((^|[\W])('.$sets[$x].')([\W]|$))#', $match['code'], $keywords)) {
							foreach ($keywords[1] as $keywordsKey => $keyword) {
								if (!empty($this->_rules['URLS'][$wordsKey])) {
									$clean = str_replace($keywords[3][$keywordsKey], '<a href="'.sprintf($this->_rules['URLS'][$wordsKey], $keywords[3][$keywordsKey]).'" style="'.$this->_rules['STYLES']['KEYWORDS'][$wordsKey].'">'.$keywords[3][$keywordsKey].'</a>', $keyword);
								} else {
									$clean = str_replace($keywords[3][$keywordsKey], '<span style="'.$this->_rules['STYLES']['KEYWORDS'][$wordsKey].'">'.$keywords[3][$keywordsKey].'</span>', $keyword);
								}
								$match['code'] = str_replace($keyword, $clean, $match['code']);
							}
						}
					}
			}

			if (preg_match_all('/<STRE:([0-9]+):([0-9]+)>/', $match['code'], $h)) {
				foreach ($h[0] as $key => $val) {
					$style = (isset($this->_rules['STYLES']['REGEXES'][$h[1][$key]])? $this->_rules['STYLES']['REGEXES'][$h[1][$key]] : '');
					$match['code'] = str_replace($val, '<span style="'.$style.'">'.$regexMatches[$h[1][$key]][0][$h[2][$key]].'</span>', $match['code']);
				}
			}
		}

		return(implode("<br />\n", explode("\n", $matches[0]['code'])));
	}

	protected function _pregQuote(&$val, $key)
	{
		$val = preg_quote(htmlentities($val));
	}

	protected function _splitKeywordSet($set, $sets = array())
	{
		if (strlen($set) > 20000) {
			if (($end = strpos($set, '|', 20000)) === false) {
				$end = strlen($set);
			}
			
			$sets[] = substr($set, 0, $end);
			$set = substr($set, $end + 1);
			$sets = $this->_splitKeywordSet($set, $sets);
		} else {
			$sets[] = $set;
		}
		
		return($sets);
	}
}
