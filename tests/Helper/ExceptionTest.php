<?php
namespace Helper
{
	/**
	 * @group Helper
	 */
	class ExceptionTest extends \PHPUnit_Framework_TestCase
	{
		/**
		 * @covers \SiTech\Helper\Exception
		 */
		public function testArguments()
		{
			$message = 'this %s my %s test';
			$this->setExpectedException('\Helper\Exception', 'this is my awesome test');
			throw new Exception($message, ['is', 'awesome']);
			$this->setExpectedException('\Helper\Exception', 'this was my totally sick test');
			throw new Exception($message, ['was', 'totally sick']);
			$this->setExpectedException('\Helper\Exception', 'pi to the fifth decimal is 3.14159');
			throw new Exception('pi to the %s decimal is %05d', ['fifth', 3.14519265359]);
		}
	}

	class Exception extends \SiTech\Helper\Exception {}
}
 