<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SiTech\Service\WeatherBug;

const UNIT_ENGLISH = 'English';
const UNIT_METRIC = 'Metric';

/**
 * Description of Base
 *
 * @author Eric Gach <eric@php-oop.net>
 */
abstract class Base
{
	/**
	 * This is the storage for the WeatherBug API key.
	 *
	 * @var string
	 */
	protected $_apiKey;
	
	/**
	 * Internal storage for the soap client.
	 *
	 * @var \SoapClient
	 */
	protected static $_soapClient;
	
	/**
	 * Base constructor for the WeatherBug service. This takes the API key and
	 * starts the soap object. The soap object is set to a static variable so
	 * each part of the service does not have to create a new object.
	 *
	 * @param string $apiKey 
	 */
	public function __construct($apiKey)
	{
		// We use this statically so its only defined once to save resources.
		if (empty(static::$_soapClient))
			static::$_soapClient = new \SoapClient('http://api.wxbug.net/webservice-v1.asmx?WSDL');

		$this->_apiKey = $apiKey;
	}
}
