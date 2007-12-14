<?php
require_once('SiTech.php') ;
SiTech::loadInterface('SiTech_Request_Interface');;

/**
 *
 */
class SiTech_Request implements SiTech_Request_Interface
{
	const METHOD_GET = 'GET';
	
	const METHOD_POST = 'POST';
	
	const TYPE_BOOL = 0;
	
	const TYPE_DOUBLE = 1;
	
	const TYPE_FLOAT = 2;
	
	const TYPE_INT = 3;
	
	const TYPE_STRING = 4;

	/**
	 * 
	 * @see SiTech_Request_Interface::getRequestData()
	 */
	public static function getRequestData($var , $method=null , $type=null)
	{
		/* no need to go on if data doesn't exist */
		if (!self::validRequestData($var, $method)) {
			return(null);
		}
		
		switch ($method)
		{
			case self::METHOD_POST:
				$val = $_POST[$var];
				break;

			default:
				$val = $_REQUEST[$var];
				break;
		}
		
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
		
		return($val);
	}
	
	/**
	 * 
	 * @see SiTech_Request_Interface::requestMethod()
	 */
	public static function requestMethod()
	{
		if (isset($_SERVER['REQUEST_METHOD'])) {
			return($_SERVER['REQUEST_METHOD']);
		} else {
			return(false);
		}
	}
	
	/**
	 * 
	 * @see SiTech_Request_Interface::validRequestData()
	 */
	public static function validRequestData ( $var , $method = null )
	{
		switch ($method)
		{
			case self::METHOD_GET:
				$val = isset($_GET[$var]);
				break;
				
			case self::METHOD_POST:
				$val = isset($_POST[$var]);
				break;
				
			default:
				$val = isset($_REQUEST[$var]);
				break;
		}
		
		return($val);
	}
}

?>
