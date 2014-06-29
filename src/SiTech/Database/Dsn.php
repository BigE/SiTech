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
 * @package SiTech\Database
 */

namespace SiTech\Database
{
	/**
	 * Create a new DSN object with the config settings passed in.
	 *
	 * Passing in an array of config options will allow this class to
	 * generate a usable DSN string to connect to the database with. Please
	 * read the PDO Drivers documentation to see what options are available
	 * for your driver.
	 *
	 * @see http://php.net/pdo.drivers
	 */
	abstract class Dsn
	{
		use \SiTech\Helper\Container;

		/**
		 * This is the string version of the DSN that was loaded. When the
		 * _generateDsn method is called, it should store the result here.
		 *
		 * @see getDsn, _generateDsn
		 * @var string
		 */
		protected $_dsn;

		/**
		 * Return the compiled dsn string.
		 *
		 * @return string
		 */
		public function __toString()
		{
			return $this->getDsn();
		}

		/**
		 * Get the compiled dsn config options as a string.
		 *
		 * Calling this method will call the _generateDsn method to compile the
		 * options to a string and return it.
		 *
		 * @return string
		 */
		public function getDsn()
		{
			$this->_generateDsn();
			return $this->_dsn;
		}

		/**
		 * Generate the DSN string from the config options provided.
		 */
		abstract protected function _generateDsn();
	}
}