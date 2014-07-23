<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 7/23/14
 * Time: 10:41 AM
 */

namespace SiTech\Helper
{
	abstract class Exception extends \Exception
	{
		public function __construct($message, array $args = [], $code = null, $inner = null)
		{
			parent::__construct(vsprintf($message, $args), $code, $inner);
		}
	}
} 