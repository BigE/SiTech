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
 * @package SiTech\DB
 */

namespace SiTech\Database
{
	/**
	 * Class Connection
	 *
	 * @package SiTech\DB
	 */
	abstract class Connection extends \PDO
	{
		/**
		 * @param array $config
		 * @param array $options
		 */
		public function __construct(array $config, array $options = [])
		{
			$user = $pass = null;

			if (isset($config['username'])) {
				$user = $config['username'];
			} elseif (isset($config['user'])) {
				$user = $config['user'];
			}

			if (isset($config['password'])) {
				$pass = $config['password'];
			} elseif (isset($config['pass'])) {
				$pass = $config['pass'];
			}

			if (!isset($config['dsn'])) {
				$config['dsn'] = $this->_generateDsn($config);
			}

			if (! isset($options[\PDO::ATTR_ERRMODE])) {
				$options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
			}

			parent::__construct($config['dsn'], $user, $pass, $options);
		}

		/**
		 * @param string $statement
		 * @param array $args
		 * @return bool|int
		 * @see http://php.net/pdo.exec
		 */
		public function exec($statement, array $args = [])
		{
			if (empty($args)) {
				return parent::exec($statement);
			} else {
				if (($statement = $this->query($statement, $args))) {
					return $statement->rowCount();
				}
			}

			return false;
		}

		/**
		 * @param string $statement
		 * @param int|array $fetch_mode
		 * @param int|string|object $arg1
		 * @param array $ctor
		 * @return bool|\PDOStatement
		 * @see http://php.net/pdo.query
		 */
		public function query($statement, $fetch_mode = null, $arg1 = null, $ctor = null)
		{
			$ret = false;

			if (is_array($fetch_mode)) {
				if (($statement = $this->prepare($statement)) !== false) {
					$ret = $statement->execute($fetch_mode);
				}
			} else {
				$ret = parent::query($statement, $fetch_mode, $arg1, $ctor);
			}

			return $ret;
		}

		abstract protected function _generateDsn(array $config);
	}
}