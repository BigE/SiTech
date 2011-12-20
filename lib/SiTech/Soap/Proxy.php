<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PetriLib_SoapProxy
 * Created: Dec 10, 2011
 *
 * @author Alexander Petrides <alexander@essential-elements.net>
 */
class SiTech_Soap_Proxy
{
    protected $_service;

	/**
	 *
	 * @param object|string $serviceClass An object or class name to perform the method call on
	 */
    public function  __construct($serviceClass)
    {
        $this->_service = is_object($serviceClass) ? $serviceClass : new $serviceClass;
    }

	public function __call($name, $arguments)
	{
		$params = count($arguments) > 0 ? get_object_vars($arguments[0]) : array();

		return array(
			$name . 'Result' => call_user_func_array(array($this->_service, $name), $params));
    }
}

?>
