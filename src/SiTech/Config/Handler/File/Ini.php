<?php
/**
 * Copyright (c) 2014 Eric Gach <eric@php-oop.net>
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
 * @copyright Copyright (c) 2014 Eric Gach <eric@php-oop.net>
 * @license MIT
 * @package SiTech\Config
 */

namespace SiTech\Config\Handler\File
{
	use SiTech\Config\Handler\NamedArgs;
	use SiTech\Config\Handler\File\Exception as FileException;
	use SiTech\Config\Handler\File\Ini\Exception;

	/**
	 * This is the ini file formatted handler for the config.
	 *
	 * @package SiTech\Config
	 * @subpackage SiTech\Config\Handler
	 */
	class Ini extends Base
	{
		/**
		 * Read an ini formatted config file.
		 *
		 * This is a simple wrapper call to parse_ini_file for loading the
		 * configuration from an ini file. We send INI_SCANNER_RAW to the
		 * function call since we handle all boolean values ourselves in the
		 * registry.
		 *
		 * @param NamedArgs $args required: filename
		 * @return array
		 * @throws \SiTech\Config\Handler\File\Exception\FileNotReadable
		 * @throws \SiTech\Config\Handler\File\Exception\FileNotFound
		 * @throws \SiTech\Config\Handler\File\Ini\Exception\ParsingError
		 */
		public function read(NamedArgs $args)
		{
			$filename = $args->offsetGet('filename', false, true);

			if (($config = @parse_ini_file($filename, true, INI_SCANNER_RAW))) {
				return $config;
			}

			if (!file_exists($filename)) {
				throw new FileException\FileNotFound($filename);
			} elseif (!is_readable($filename)) {
				throw new FileException\FileNotReadable($filename);
			}

			throw new Exception\ParsingError($filename);
		}

		/**
		 * Write an ini formatted config file.
		 *
		 * Simply use the file functions to write the ini formatted file. All
		 * values should be broken down accordingly to be written out to a file
		 * so when reading it can all be parsed into an array properly.
		 *
		 * @param NamedArgs $args required: filename, config
		 * @throws \SiTech\Config\Handler\File\Exception\FileNotWritable
		 * @todo Needs more error handling...
		 */
		public function write(NamedArgs $args)
		{
			$filename = $args->offsetGet('filename', false, true);
			$config = $args->offsetGet('config', false, true);

			if (($fp = @fopen($filename, 'w')) !== false) {
				foreach ($config as $section => $options) {
					fwrite($fp, '['.$section.']'.PHP_EOL);
					foreach ($options as $option => $value) {
						fwrite($fp, $option.'='.$value.PHP_EOL);
					}
				}
				fclose($fp);
				return;
			}

			if (!is_writable($filename)) {
				throw new FileException\FileNotWritable($filename);
			}
		}
	}
}