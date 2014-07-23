<?php
namespace SiTech\Config
{
	abstract class Exception extends \SiTech\Helper\Exception
	{}

	class DuplicateKey extends Exception
	{
		public function __construct($key, $code = null, $inner = null)
		{
			parent::__construct('The key %s already exists in the configuration', [$key], $code, $inner);
		}
	}

	class MissingKey extends Exception
	{
		public function __construct($key, $code = null, $inner = null)
		{
			parent::__construct('The key %s is not currently present in the configuration', [$key], $code, $inner);
		}
	}
}