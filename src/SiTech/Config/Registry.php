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

namespace SiTech\Config
{
	class Registry
	{
		use \SiTech\Helper\Container {
			get as containerGet;
		}

		use \SiTech\Helper\Singleton;

		public function get($key)
		{
			try {
				return $this->offsetGet($key, false, true);
			} catch (\Exception $inner) {
				require_once __DIR__.'/Exception.php';
				throw new MissingKey($key, null, $inner);
			}
		}

		/**
		 * Return either true or false based on the value of the key
		 *
		 * If the value of the key is 'true' (string/bool), 'yes', 'on' or '1'
		 * (string/int) the value will be interpreted as true otherwise false
		 * is assumed and returned.
		 *
		 * @param $key
		 * @return bool
		 */
		public function getBoolean($key)
		{
			if (($v = $this->get($key, false)) && in_array($v, [true, 'true', 'yes', 'on', '1', 1], true)) {
				return true;
			}

			return false;
		}

		/**
		 * Return a float value for the settings key specified.
		 *
		 * @param string $key
		 * @return float
		 */
		public function getFloat($key)
		{
			return (float)$this->get($key);
		}

		/**
		 * Return an integer value for the settings key specified.
		 *
		 * @param string $key
		 * @return int
		 */
		public function getInteger($key)
		{
			return (int)$this->get($key);
		}

		/**
		 * Simple wrapper function to check if a key exists.
		 *
		 * @param string $key
		 * @return bool
		 * @see \SiTech\Helper\Container::offsetExists
		 */
		public function hasKey($key)
		{
			return $this->offsetExists($key);
		}

		public function load()
		{}

		public function save()
		{}

		/**
		 * Set a value to the key specified.
		 *
		 * This does a simple check to see if the key is already set. If it is
		 * set and strict is true, a DuplicateKey exception will be thrown.
		 *
		 * @param string $key
		 * @param mixed $value
		 * @param bool $strict
		 * @return Registry
		 * @throws DuplicateKey
		 */
		public function set($key, $value, $strict = false)
		{
			if (!$this->offsetExists($key) || $strict === false) {
				$this->offsetSet($key, $value);
				return $this;
			}

			require_once __DIR__.'/Exception.php';
			throw new DuplicateKey($key);
		}
	}
}