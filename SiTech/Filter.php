<?php
/**
 * 
 */

/**
 * @see SiTech_Filter_Interface
 */
require_once('SiTech/Filter/Interface.php');

/**
 *
 */
class SiTech_Filter implements SiTech_Filter_Interface
{
	const METHOD_GET = 1;
	const METHOD_POST = 0;
	const TYPE_BOOL = 1;
	const TYPE_DOUBLE = 2;
	const TYPE_FLOAT = 3;
	const TYPE_INT = 4;
	const TYPE_STRING = 5;

	/**
	 * Get request data by the specified method. If no method is specified, it
	 * defaults to getting it from $_REQUEST
	 *
	 * @param string $var
	 * @param int $method SiTech_Filter::METHOD_* constant
	 * @param int $type SiTech_Filter::TYPE_* constant
	 * @return mixed
	 * @see SiTech_Filter_Interface::getRequestData()
	 */
	public static function getRequestData($var, $method = null, $type = null)
	{
		/* no need to go on if data doesn't exist */
		if (!self::validRequestData($var, $method)) {
			return(null);
		}

		if (is_null($method)) $method = 'default';
		switch ($method)
		{
			case self::METHOD_GET:
				$val = $_GET[$var];
				break;

			case self::METHOD_POST:
				$val = $_POST[$var];
				break;

			default:
				$val = $_REQUEST[$var];
				break;
		}
		
		if (!is_null($type)) {
			switch ($type)
			{
				case self::TYPE_BOOL:
					$val = (bool)$val;
					break;
					
				case self::TYPE_DOUBLE:
					$val = (double)$val;
					break;
					
				case self::TYPE_FLOAT:
					$val = (float)$val;
					break;
					
				case self::TYPE_INT:
					$val = (int)$val;
					break;
	
				case self::TYPE_STRING:
					$val = (string)$val;
					break;
			}
		}
		
		return($val);
	}
	
	/**
	 * Return the request method for the current request. If it is not set, this
	 * method will return null.
	 *
	 * @return string
	 * @see SiTech_Filter_Interface::requestMethod()
	 */
	public static function requestMethod()
	{
		if (isset($_SERVER['REQUEST_METHOD'])) {
			return(strtolower($_SERVER['REQUEST_METHOD']));
		} else {
			return(false);
		}
	}
	
	/**
	 * Check if the request contains valid data for the specified variable.
	 *
	 * @param string $var
	 * @param int $method SiTech_Filter::METHOD_* constant
	 * @return bool
	 * @see SiTech_Filter_Interface::validRequestData()
	 */
	public static function validRequestData ($var, $method = null)
	{
		if (is_null($method)) $method = 'default';
		switch ($method)
		{
			case self::METHOD_GET:
				$val = empty($_GET[$var]);
				break;
				
			case self::METHOD_POST:
				$val = empty($_POST[$var]);
				break;
				
			default:
				$val = empty($_REQUEST[$var]);
				break;
		}
		
		return(!$val);
	}
}

?>
