<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 7/28/14
 * Time: 3:17 PM
 */

namespace SiTech\Helper
{
	trait Env
	{
		protected $env;
		protected static $staticEnv;

		public function getEnv()
		{
			return $this->env;
		}

		public static function getStaticEnv()
		{
			return static::$staticEnv;
		}

		public function setEnv($env)
		{
			$this->env = $env;
			return $this;
		}

		public static function setStaticEnv($env)
		{
			static::$staticEnv = $env;
		}

		protected function prependEnv($string, $separator = ':')
		{
			if (!empty($this->env)) {
				return $this->env.$separator.$string;
			} elseif (defined('SITECH_ENV')) {
				return SITECH_ENV.$separator.$string;
			} else {
				return $string;
			}
		}
	}
} 