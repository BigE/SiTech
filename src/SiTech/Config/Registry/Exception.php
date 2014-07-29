<?php
namespace SiTech\Config\Registry\Exception
{
	/**
	 * Class Exception
	 *
	 * @package SiTech\Config
	 */
	abstract class Exception extends \SiTech\Helper\Exception
	{}

	/**
	 * Class DuplicateSection
	 *
	 * @package SiTech\Config
	 */
	class DuplicateSection extends Exception
	{
		public function __construct($section, $code = null, $inner = null)
		{
			parent::__construct('The section %s already exists in the configuration', [$section], $code, $inner);
		}
	}

	class MissingOption extends Exception
	{
		public function __construct($section, $option, $code = null, $inner = null)
		{
			parent::__construct('The option %s is not currently set in the section %s of the configuration', [$option, $section], $code, $inner);
		}
	}

	/**
	 * Class MissingSection
	 *
	 * @package SiTech\Config
	 */
	class MissingSection extends Exception
	{
		public function __construct($section, $code = null, $inner = null)
		{
			parent::__construct('The section %s is not currently present in the configuration', [$section], $code, $inner);
		}
	}

	class UnexpectedValue extends \SiTech\Helper\Exception\UnexpectedValue {}
}