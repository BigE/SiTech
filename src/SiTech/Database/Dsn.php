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
	abstract class Dsn implements \ArrayAccess
	{
		/**
		 * This is the array of config items that are set on the object.
		 *
		 * @var array
		 */
		protected $_config = [];

		/**
		 * This is the string version of the DSN that was loaded.
		 *
		 * @see getDsn
		 * @var string
		 */
		protected $_dsn;

		/**
		 * Create a new DSN object with the config settings passed in.
		 *
		 * Passing in an array of config options will allow this class to
		 * generate a usable DSN string to connect to the database with. Please
		 * read the PDO Drivers documentation to see what options are available
		 * for your driver.
		 *
		 * @param array $config
		 * @see http://php.net/pdo.drivers
		 */
		public function __construct(array $config)
		{
			$this->_config = $config;
		}

		/**
		 * Allows access to config settings as properties of the object.
		 *
		 * The DSN object should return all configuration settings through this
		 * method as properties of the object itself. This should make accessing
		 * them easier.
		 *
		 * @param $name Name of the setting to get the value of
		 * @return string|null
		 */
		public function __get($name)
		{
			if (isset($this->_config[$name])) {
				return $this->_config[$name];
			}

			return null;
		}

		/**
		 * Check if a config option is set by checking it as a property.
		 *
		 * This simply checks if the configuration item is set by returning the
		 * value of isset()
		 *
		 * @param $name Name of the setting to check if is set
		 * @return bool
		 */
		public function __isset($name)
		{
			return isset($this->_config[$name]);
		}

		/**
		 * Set a configuration option to the specified value.
		 *
		 * This will simply set a value to a configuration option. There are no
		 * checks in place for overwriting variables.
		 *
		 * @param $name
		 * @param $value
		 */
		public function __set($name, $value)
		{
			$this->_config[$name] = $value;
		}

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
		 * Whether an offset exists in the config.
		 *
		 * This simply checks if a configuration option exists by using isset().
		 *
		 * @param mixed $offset
		 * @return bool
		 */
		public function offsetExists($offset)
		{
			return isset($this->_config[$offset]);
		}

		/**
		 * Retrieve an offset from the config.
		 *
		 * By setting $prefix to true it will return the offset name combined
		 * to the value as "name=value". If $required is set to true and the
		 * offset is not set, an exception will be thrown.
		 *
		 * @param mixed $offset
		 * @param bool $prefix
		 * @param bool $required
		 * @return mixed|string
		 * @throws \Exception
		 */
		public function offsetGet($offset, $prefix = false, $required = false)
		{
			$value = '';

			if (is_array($offset)) {
				if ($prefix === true) {
					$prefix = $offset[0].'=';
				}

				foreach ($offset as $item) {
					if ($this->offsetExists($item)) {
						$offset = $item;
						break;
					}
				}
			} elseif ($prefix === true) {
				$prefix = $offset.'=';
			}

			if ($this->offsetExists($offset)) {
				$value = $prefix.$this->_config[$offset];
			} elseif ($required) {
				throw new \Exception();
			}

			return $value;
		}

		/**
		 * Set a value to the config name.
		 *
		 * @param mixed $offset
		 * @param mixed $value
		 */
		public function offsetSet($offset, $value)
		{
			$this->_config[$offset] = $value;
		}

		/**
		 * Unset the offset from the config.
		 *
		 * @param mixed $offset
		 */
		public function offsetUnset($offset)
		{
			unset($this->_config[$offset]);
		}

		/**
		 * Generate the DSN string from the config options provided.
		 */
		abstract protected function _generateDsn();
	}
}