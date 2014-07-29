<?php
namespace SiTech\Helper
{
	abstract class Exception extends \Exception
	{
		public function __construct($message, array $args = [], $code = null, \Exception $inner = null)
		{
			parent::__construct(vsprintf($message, $args), $code, $inner);
		}
	}
}

namespace SiTech\Helper\Exception
{
	use \SiTech\Helper\Exception;

	class UnexpectedValue extends Exception {}
} 