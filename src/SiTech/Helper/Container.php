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
 * @package SiTech\Helper
 */

namespace SiTech\Helper
{
	/**
	 * The container trait implements \ArrayAccess and \Countable
	 *
	 * @package SiTech\Helper
	 */
	trait Container
	{
		/**
		 * This is the array of config items that are set on the object.
		 *
		 * @var array
		 */
		protected $container = [];

		/**
		 * Create the attributes array that is to be used for the container.
		 *
		 * Here yeu can pass in the array of items you want to set for the
		 * container. If you do not pass any in, they may be set at a later
		 * time.
		 *
		 * @param array $attributes
		 */
		public function __construct(array $attributes = [])
		{
			$this->container = $attributes;
		}

		/**
		 * @param $name
		 * @param $params
		 * @return $this
		 */
		public function __call($name, $params)
		{
			$this->container[$name] = (count($params) > 0)? $params[0] : true;
			return $this;
		}

		/**
		 * Allows access to attributes as properties of the object.
		 *
		 * The DSN object should return all configuration settings through this
		 * method as properties of the object itself. This should make accessing
		 * them easier.
		 *
		 * @param $key Name of the setting to get the value of
		 * @return string|null
		 */
		public function __get($key)
		{
			return $this->get($key);
		}

		/**
		 * Check if a config option is set by checking it as a property.
		 *
		 * This simply checks if the configuration item is set by returning the
		 * value of isset()
		 *
		 * @param $key Name of the setting to check if is set
		 * @return bool
		 */
		public function __isset($key)
		{
			return $this->offsetExists($key);
		}

		/**
		 * Set a configuration option to the specified value.
		 *
		 * This will simply set a value to a configuration option. There are no
		 * checks in place for overwriting variables.
		 *
		 * @param $key
		 * @param $value
		 */
		public function __set($key, $value)
		{
			$this->offsetSet($key, $value);
		}

		/**
		 * (PHP 5 &gt;= 5.1.0)<br/>
		 * Count elements of an object
		 * @link http://php.net/manual/en/countable.count.php
		 * @return int The custom count as an integer.
		 * </p>
		 * <p>
		 * The return value is cast to an integer.
		 */
		public function count()
		{
			return count($this->container);
		}

		public function get($key, $default = null)
		{
			if ($this->offsetExists($key)) {
				return $this->offsetGet($key);
			}

			return $default;
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
			return isset($this->container[$offset]);
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
				$value = $this->container[$offset];

				if ($prefix) {
					$value = $prefix.$value;
				}
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
			$this->container[$offset] = $value;
		}

		/**
		 * Unset the offset from the config.
		 *
		 * @param mixed $offset
		 */
		public function offsetUnset($offset)
		{
			unset($this->container[$offset]);
		}
	}
}