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
	 * Configuration registry for handling settings used by the application.
	 *
	 * This configuration registry is loosely based on the python ConfigParser
	 * module. The registry itself is just a collection of settings that can be
	 * set or retrieved. To actually read or save the configuration, you will need
	 * a Handler as defined in the Handler interface. This allows the config
	 * to be stored anywhere in any format.
	 *
	 * @package SiTech\Config
	 */
	class Registry
	{
		use \SiTech\Helper\Env;
		use \SiTech\Helper\Singleton;

		/**
		 * This setting dictates the global interpolation of the config registry.
		 *
		 * @see interpolate
		 * @var bool
		 */
		protected $interpolation = true;

		/**
		 * This is the configuration storage array. All settings will be stored
		 * here during runtime.
		 *
		 * @var array
		 */
		protected $registry = [];

		/**
		 * Setup the configuration registry.
		 *
		 * @param bool $interpolation
		 */
		public function __construct($interpolation = true)
		{
			$this->interpolation = $interpolation;
		}

		/**
		 * Add a section to the configuration.
		 *
		 * All config options require a section to be placed into. This is how
		 * all sections are created for use. If the section already exists in
		 * the configuration, an exception will be thrown.
		 *
		 * @param string $section
		 * @return $this
		 * @throws Registry\Exception\DuplicateSection
		 */
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
		 * This is the most basic way of getting values out of the configuration
		 * registry. Interpolation can be overridden by passing the argument
		 * value to this method. Any variables used in interpolation can also
		 * be provided here. The fifth argument $default, is strictly checked
		 * internally using func_num_args() to see if it was passed in. If it
		 * is, the value from $default is used otherwise an exception is thrown.
		 *
		 * @param string $section
		 * @param string $option
		 * @param bool $interpolation
		 * @param array $vars
		 * @param mixed $default
		 * @return mixed|string
		 * @throws Exception\MissingOption
		 * @throws Exception\MissingSection
		 */
		public function get($section, $option, $interpolation = null, array $vars = [], $default = null)
		{
			if (($s = $this->section($section))) {
				if ($this->hasOption($section, $option)) {
					return $this->interpolate($this->registry[$s][$option], $vars, $interpolation);
				} elseif (func_num_args() > 4) {
					return $this->interpolate($default, $vars, $interpolation);
				}
			}

			if ($s === false) {
				throw new Exception\MissingSection($section);
			}

			throw new Exception\MissingOption($section, $option);
		}

		/**
		 * Get a boolean value from the configuration.
		 *
		 * This does not simply typecast the value from the configuration. It
		 * will pull the value out, then strictly check it against allowed
		 * boolean values (case insensitive). If an invalid value is found, an
		 * exception will be thrown.
		 *
		 * For a boolean value of `true` the following values are
		 * accepted:
		 * - yes
		 * - on
		 * - 1 (string|int)
		 * - true (string|bool)
		 *
		 * For a boolean value of `false` the following values are accepted:
		 * - no
		 * - off
		 * - 0 (string|int)
		 * - false (string|bool)
		 *
		 * @param string $section
		 * @param string $option
		 * @return bool
		 * @see get
		 * @throws Registry\Exception\UnexpectedValue
		 */
		public function getBoolean($section, $option)
		{
			$v = $this->get($section, $option);

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

		/**
		 * Get a float value from the configuration.
		 *
		 * @param string $section
		 * @param string $option
		 * @see get
		 * @return float
		 */
		public function getFloat($section, $option)
		{
			return (float)$this->get($section, $option);
		}

		/**
		 * Get an integer value from the configuration.
		 *
		 * @param string $section
		 * @param string $option
		 * @see get
		 * @return int
		 */
		public function getInt($section, $option)
		{
			return (int)$this->get($section, $option);
		}

		/**
		 * Return the class level setting for interpolation.
		 *
		 * @return bool
		 */
		public function getInterpolation()
		{
			return $this->interpolation;
		}

		/**
		 * Check if a specific option is set within the specified section.
		 *
		 * @param string $section
		 * @param string $option
		 * @return bool
		 */
		public function hasOption($section, $option)
		{
			return (($section = $this->section($section)) && isset($this->registry[$section][$option]));
		}

		/**
		 * Check if a specific section is defined within the configuration.
		 *
		 * @param string $section
		 * @return bool
		 */
		public function hasSection($section)
		{
			return (bool)$this->section($section);
		}

		/**
		 * Retrieve all options that are available.
		 *
		 * This simply pulls all configuration options that are available in the
		 * given section. If the section given is not found, an exception will
		 * be thrown. If no section is given, the entire configuration will be
		 * returned.
		 *
		 * @param string $section
		 * @return array
		 * @throws Registry\Exception\MissingSection
		 */
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

		/**
		 * Return an array of the available options for the specified section.
		 *
		 * This simply runs array_keys on the configuration section passed into
		 * the function. If the section is not found an exception will be thrown.
		 *
		 * @param string $section
		 * @return array
		 * @throws Registry\Exception\MissingSection
		 */
		public function options($section)
		{
			if (($s = $this->section($section))) {
				return array_keys($this->registry[$s]);
			}

			throw new Exception\MissingSection($section);
		}

		/**
		 * Read the configuration into the registry.
		 *
		 * Simply pass the handler and arguments in and this function will call
		 * the read method of the handler. The handler should return an array
		 * that will be merged into the current configuration.
		 *
		 * @param Handler $handler
		 * @param NamedArgs $args
		 * @return $this
		 * @todo check return value and merge result into configuration
		 */
		public function read(Handler $handler, NamedArgs $args)
		{
			$this->registry = $handler->read($args);
			return $this;
		}

		/**
		 * Remove an option from the specified section of the configuration.
		 *
		 * There is a simple check to see if the option exists. If it does
		 * we simply unset() the option specified and return true. If the
		 * option does not exist, return false. If the section is not found
		 * an exception will be thrown.
		 *
		 * @param string $section
		 * @param string $option
		 * @return bool
		 * @throws Registry\Exception\MissingSection
		 */
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

		/**
		 * Remove an entire section from the configuration.
		 *
		 * If the section specified is found we simply unset() it. If it does
		 * not exist in the configuration then we return false.
		 *
		 * @param string $section
		 * @return bool
		 */
		public function removeSection($section)
		{
			if (($s = $this->section($section))) {
				unset($this->registry[$s]);
				return true;
			}

			return false;
		}

		/**
		 * Get an array of the available sections in the configuration.
		 *
		 * @return array
		 */
		public function sections()
		{
			return array_keys($this->registry);
		}

		/**
		 * Set the value of an option in the given section.
		 *
		 * Set the value and return the Registry object for easy method
		 * chaining. If the section does not exist an exception will be thrown.
		 *
		 * @param string $section
		 * @param string $option
		 * @param mixed $value
		 * @return Registry
		 * @throws Registry\Exception\MissingSection
		 */
		public function set($section, $option, $value)
		{
			if (($s = $this->section($section))) {
				$this->registry[$s][$option] = $value;
				return $this;
			}

			throw new Exception\MissingSection($section);
		}

		/**
		 * Write the configuration using the handler specified.
		 *
		 * @param Handler $handler
		 * @param NamedArgs $args
		 * @return Registry
		 */
		public function write(Handler $handler, NamedArgs $args)
		{
			$handler->write($args);
			return $this;
		}

		/**
		 * Use vsprintf to interpolate configuration strings with values provided.
		 *
		 * If interpolation is enabled, either through the class or the method
		 * call itself, this will use vsprintf to pass the variables into the
		 * string from the configuration. See http://php.net/sprintf for
		 * formatting.
		 *
		 * @param mixed $value
		 * @param array $vars
		 * @param bool $raw
		 * @return string
		 * @see http://php.net/vsprintf
		 */
		protected function interpolate($value, array $vars, $raw)
		{
			if (is_string($value) && !empty($vars)
				&& (
					($this->interpolation === true && $raw !== true)
					|| ($this->interpolation === false && $raw === false)
				)
			) {
				return vsprintf($value, $vars);
			}

			return $value;
		}

		/**
		 * Return the full section name with the environment.
		 *
		 * This helps the Registry use environments so you can have a config
		 * sections for production, staging, development, etc. without having
		 * a totally duplicate file. We check if the section exists unless the
		 * return is forced.
		 *
		 * @param string $section
		 * @param bool $force_return
		 * @return bool|string
		 */
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