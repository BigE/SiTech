<?php
namespace SiTech\Helper
{
	trait Singleton
	{
		/**
		 * @var Singleton
		 */
		protected static $instance;

		/**
		 * @return Singleton
		 */
		public static function getInstance()
		{
			if (empty(static::$instance)) {
				static::$instance = new static;
			}

			return static::$instance;
		}
	}
} 