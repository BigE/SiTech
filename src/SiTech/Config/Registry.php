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
	use SiTech\Config\Handler\Handler;
	use SiTech\Config\Handler\NamedArgs;
	use SiTech\Config\Registry\Exception;

	/**
	 * Class Registry
	 *
	 * @package SiTech\Config
	 */
	class Registry
	{
		use \SiTech\Helper\Env;
		use \SiTech\Helper\Singleton;

		protected $interpolation = true;
		protected $registry = [];

		public function __construct($interpolation = true)
		{
			$this->interpolation = $interpolation;
		}

		public function addSection($section)
		{
			$s = $this->section($section, true);

			if (($s === $section && !isset($this->registry[$section])) || ($s !== $section && !isset($this->registry[$s]))) {
				$section = $s;
				$this->registry[$section] = [];
				return $this;
			}

			throw new Exception\DuplicateSection($section);
		}

		/**
		 * Get a value from the configuration.
		 *
		 * @param $section
		 * @param $option
		 * @param bool $raw
		 * @param array $vars
		 * @param mixed $default
		 * @return mixed|string
		 * @throws Exception\MissingOption
		 * @throws Exception\MissingSection
		 */
		public function get($section, $option, $raw = false, array $vars = [])
		{
			if (func_num_args() > 4) {
				// *sigh* starts from 0, I should know this
				$default = func_get_arg(4);
			}

			if (($s = $this->section($section))) {
				if ($this->hasOption($section, $option)) {
					return $this->interpolate($this->registry[$s][$option], $vars, $raw);
				} elseif (isset($default)) {
					return $this->interpolate($default, $vars, $raw);
				}
			}

			if ($s === false) {
				throw new Exception\MissingSection($section);
			}

			throw new Exception\MissingOption($section, $option);
		}

		public function getBoolean($section, $option, $raw = false, array $vars = [])
		{
			$v = call_user_func_array([$this, 'get'], func_get_args());

			if (is_string($v)) {
				$v = strtolower($v);
			}

			if (in_array($v, ['1', 'yes', 'on', 'true', 1, true], true)) {
				return true;
			} elseif (in_array($v, ['0', 'no', 'off', 'false', 0, false], true)) {
				return false;
			}

			throw new Exception\UnexpectedValue('Expecting boolean value, got %s', [$v]);
		}

		public function getFloat($section, $option, $raw = false, array $vars = [])
		{
			return (float)call_user_func_array([$this, 'get'], func_get_args());
		}

		public function getInt($section, $option)
		{
			return (int)call_user_func_array([$this, 'get'], func_get_args());
		}

		public function getInterpolation()
		{
			return $this->interpolation;
		}

		public function hasOption($section, $option)
		{
			return (($section = $this->section($section)) && isset($this->registry[$section][$option]));
		}

		public function hasSection($section)
		{
			return (bool)$this->section($section);
		}

		public function items($section = null)
		{
			if (empty($section)) {
				return $this->registry;
			}

			if (($s = $this->section($section))) {
				return $this->registry[$s];
			}

			throw new Exception\MissingSection($section);
		}

		public function options($section)
		{
			if (($s = $this->section($section))) {
				return array_keys($this->registry[$s]);
			}

			throw new Exception\MissingSection($section);
		}

		public function read(Handler $handler, NamedArgs $args = null)
		{
			$this->registry = $handler->read($args);
			return $this;
		}

		public function removeOption($section, $option)
		{
			if (($s = $this->section($section))) {
				if (isset($this->registry[$s][$option])) {
					unset($this->registry[$s][$option]);
					return true;
				}

				return false;
			}

			throw new Exception\MissingSection($section);
		}

		public function removeSection($section)
		{
			if (($s = $this->section($section))) {
				unset($this->registry[$s]);
				return true;
			}

			return false;
		}

		public function sections()
		{
			return array_keys($this->registry);
		}

		public function set($section, $option, $value)
		{
			if (($s = $this->section($section))) {
				$this->registry[$s][$option] = $value;
				return $this;
			}

			throw new Exception\MissingSection($section);
		}

		public function write(Handler $handler, NamedArgs $args = null)
		{
			$handler->write($args);
			return $this;
		}

		protected function interpolate($value, array $vars, $raw)
		{
			if (is_string($value) && !empty($vars) && $this->interpolation === true && $raw !== true) {
				return vsprintf($value, $vars);
			}

			return $value;
		}

		protected function section($section, $force_return = false)
		{
			$envSection = $this->prependEnv($section);
			if (isset($this->registry[$envSection]) || ($section !== $envSection && $force_return === true)) {
				return $envSection;
			} elseif (isset($this->registry[$section]) || $force_return === true) {
				return $section;
			}

			return false;
		}
	}
}