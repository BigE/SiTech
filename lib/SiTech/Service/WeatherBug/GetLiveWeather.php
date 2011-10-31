<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SiTech\Service\WeatherBug;

require_once('Base.php');

/**
 * Description of GetLiveWeather
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class GetLiveWeather extends Base
{
	/**
	 * Setting to control amount of data fetched from the server. 
	 */
	const ATTR_COMPACT = 1;
	
	/**
	 * Attributes storage for the GetLiveWeather object.
	 *
	 * @var array
	 */
	protected $_attributes = array();

	public function __construct($apiKey, array $options = array())
	{
		parent::__construct($apiKey);
		foreach ($options as $k => $v) {
			$this->setAttribute($k,$v);
		}
	}
	
	/**
	 * Get the weather information for a city by the city code that WeatherBug
	 * provides. The city code can be retreived through the search functions.
	 *
	 * @param string $cityCode
	 * @param int $unit Use one of the SiTech\Service\WeatherBug\UNIT_* constants.
	 * @return stdObject[]
	 * @throws \Exception 
	 */
	public function ByCityCode($cityCode, $unit = UNIT_METRIC)
	{
		$params = array(
			'cityCode' => $cityCode,
			'unittype' => $unit,
			'ACode' => $this->_apiKey,
		);
		$return = ($this->getAttribute(self::ATTR_COMPACT))? static::$_soapClient->GetLiveCompactWeatherByCityCode($params) : static::$_soapClient->GetLiveWeatherByCityCode($params);

		if (isset($return->GetLiveWeatherByCityCodeResult))
			return($return->GetLiveWeatherByCityCodeResult);
		elseif (isset($return->GetLiveCompactWeatherByCityCodeResult))
			return($return->GetLiveCompactWeatherByCityCodeResult);
		else
			throw new \Exception('Failed to retreive weather for ' . $cityCode);
	}

	/**
	 * Get the weather information for a city by the station id. The station id
	 * can be retreived through the search functions.
	 *
	 * @param string $stationId
	 * @param int $unit
	 * @return stdObject[]
	 * @throws \Exception 
	 */
	public function ByStationId($stationId, $unit = UNIT_METRIC)
	{
		$params = array(
			'stationid' => $stationId,
			'unittype' => $unit,
			'ACode' => $this->_apiKey,
		);
		$return = ($this->getAttribute(self::ATTR_COMPACT))? static::$_soapClient->GetLiveCompactWeatherByStationID($params) : static::$_soapClient->GetLiveWeatherByStationID($params);

		if (isset($return->GetLiveWeatherByStationIDResult))
			return($return->GetLiveWeatherByStationIDResult);
		elseif (isset($return->GetLiveCompactWeatherByStationIDResult))
			return($return->GetLiveCompactWeatherByStationIDResult);
		else
			throw new \Exception('Failed to retreive weather for '.$stationId);
	}

	/**
	 * Get the weather information for a city by the zip code. WeatherBug
	 * indicates that this only works for US cities.
	 *
	 * @param string $zipCode
	 * @param int $unit
	 * @return stdObject[]
	 * @throws \Exception 
	 */
	public function ByZipCode($zipCode, $unit = UNIT_METRIC)
	{
		$params = array(
			'zipCode' => $zipCode,
			'unittype' => $unit,
			'ACode' => $this->_apiKey,
		);
		$return = ($this->getAttribute(self::ATTR_COMPACT))? static::$_soapClient->GetLiveCompactWeatherByUSZipCode($params) : static::$_soapClient->GetLiveWeatherByUSZipCode($params);

		if (isset($return->GetLiveWeatherByUSZipCodeResult))
			return($return->GetLiveWeatherByUSZipCodeResult);
		elseif(isset($return->GetLiveCompactWeatherByUSZipCodeResult))
			return($return->GetLiveCompactWeatherByUSZipCodeResult);
		else
			throw new \Exception('Failed to retreive weather for ' . $zipCode);
	}

	/**
	 *
	 * @param int $attr
	 * @return mixed
	 */
	public function getAttribute($attr)
	{
		if (isset($this->_attributes[$attr]))
			return($this->_attributes[$attr]);
		else
			return(null);
	}

	/**
	 *
	 * @param int $attr
	 * @param mixed $value 
	 */
	public function setAttribute($attr, $value)
	{
		$this->_attributes[$attr] = $value;
	}
}
