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

namespace SiTech\Config\Registry\Exception
{
	/**
	 * Class Exception
	 *
	 * @package SiTech\Config
	 */
	abstract class Exception extends \SiTech\Helper\Exception
	{}

	/**
	 * Class DuplicateSection
	 *
	 * @package SiTech\Config
	 */
	class DuplicateSection extends Exception
	{
		public function __construct($section, $code = null, $inner = null)
		{
			parent::__construct('The section %s already exists in the configuration', [$section], $code, $inner);
		}
	}

	class MissingOption extends Exception
	{
		public function __construct($section, $option, $code = null, $inner = null)
		{
			parent::__construct('The option %s is not currently set in the section %s of the configuration', [$option, $section], $code, $inner);
		}
	}

	/**
	 * Class MissingSection
	 *
	 * @package SiTech\Config
	 */
	class MissingSection extends Exception
	{
		public function __construct($section, $code = null, $inner = null)
		{
			parent::__construct('The section %s is not currently present in the configuration', [$section], $code, $inner);
		}
	}

	class UnexpectedValue extends \SiTech\Helper\Exception\UnexpectedValue {}
}