<?php
/**
 * Copyright (c) 2015 Eric Gach <eric@php-oop.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright Copyright (c) 2015 Eric Gach <eric@php-oop.net>
 * @license MIT
 * @package SiTech\Config
 */

namespace SiTech\Config\Handler\File
{
	use SiTech\Config\Handler\NamedArgs;
	use SiTech\Config\Handler\File\Exception as FileException;
	use SiTech\Config\Handler\File\Xml\Exception;

	/**
	 * Class Xml
	 *
	 * @package SiTech\Config;
	 * @subpackage SiTech\Config\Handler\File
	 */
	class Xml extends Base
	{
		private $_config = array();

		private $_key;

		private $_section;

		public function __construct()
		{
			$this->sax = xml_parser_create();

			xml_parser_set_option($this->sax, XML_OPTION_SKIP_WHITE, 1);
			xml_parser_set_option($this->sax, XML_OPTION_CASE_FOLDING, 0);
			xml_set_element_handler(
				$this->sax,
				function ($sax, $tag, $attribute) {
					$this->start_element($tag, $attribute);
				},
				function ($sax, $tag) {
					$this->end_element($tag);
				}
			);
			xml_set_character_data_handler(
				$this->sax,
				function ($sax, $data) {
					$this->content_element($data);
				}
			);
		}

		public function __destruct()
		{
			xml_parser_free($this->sax);
		}

		public function read(NamedArgs $args)
		{
			$filename = $args->offsetGet('filename', false, true);

			if (($config = @file_get_contents($filename)) !== false
				&& xml_parse($this->sax, $config, true) === 1
			) {
				return $this->_config;
			}

			if (!file_exists($filename)) {
				throw new FileException\FileNotFound($filename);
			} elseif (!is_readable($filename)) {
				throw new FileException\FileNotReadable($filename);
			}

			throw new Exception\ParsingError(
				$filename,
				xml_error_string(xml_get_error_code($this->sax)),
				xml_get_current_line_number($this->sax),
				xml_get_current_column_number($this->sax),
				xml_get_current_byte_index($this->sax)
			);
		}

		public function write(NamedArgs $args)
		{
			$filename = $args->offsetGet('filename', false, true);
			$config = $args->offsetGet('config', false, true);

			if (($fp = @fopen($filename, 'w')) !== false) {
				fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
				fwrite($fp, '<config>' . PHP_EOL);
				foreach ($config as $section => $value) {
					fwrite($fp, '<section name="' . $section . '">' . PHP_EOL);
					foreach ($value as $k => $v) {
						fwrite($fp, '<key name="' . $k . '">' . $v . '</key>' . PHP_EOL);
					}
					fwrite($fp, '</section>' . PHP_EOL);
				}
				fwrite($fp, '</config>');
				fclose($fp);
				return;
			}

			if (!is_writable($filename)) {
				throw new FileException\FileNotWritable($filename);
			}
		}

		private function content_element($data)
		{
			if (isset($this->_section) && isset($this->_key)) {
				if (!isset($this->_config[$this->_section])) {
					$this->_config[$this->_section] = [];
				}

				$this->_config[$this->_section][$this->_key] = $data;
			}
		}

		private function end_element($tag)
		{
			switch ($tag) {
				case 'section':
					unset($this->_section);
					break;
				case 'key':
					unset($this->_key);
					break;
			}
		}

		private function start_element($tag, $attribute)
		{
			switch ($tag) {
				case 'section':
					$this->_section = $attribute['name'];
					break;
				case 'key':
					$this->_key = $attribute['name'];
					break;
			}
		}
	}
}
