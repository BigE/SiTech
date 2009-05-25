<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Uri
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class SiTech_Uri
{
	protected $_requestUri;
	
	/**
	 * If no URI is passed to the constructor, this class will parse the current
	 * REQUEST_URI passed in by the web server.
	 *
	 * @param string $uri URI to parse
	 */
	public function __construct($uri = null)
	{
		if (is_null($uri)) {
			$uri = ((isset($_SERVER['HTTPS']))? 'https://' : 'http://').$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		}

		$this->_requestUri = parse_url($uri);
	}
	
	public function __toString()
	{
		return($this->getUri());
	}

	public function getPath()
	{
		return($this->_requestUri['path']);
	}

	public function getUri($withQuery = true, $withFragment = true)
	{
		$uri = $this->_requestUri['scheme'];
		$uri .= '://'.$this->_requestUri['host'];
		if (!empty($this->_requestUri['port']) && $this->_requestUri['port'] != 80) {
			$uri .= ':'.$this->_requestUri['port'];
		}
		$uri .= $this->_requestUri['path'];

		if (!empty($this->_requestUri['query']) && $withQuery) {
			$uri .= '?'.$this->_requestUri['query'];
		}

		if (!empty($this->_requestUri['fragment']) && $withFragment) {
			$uri .= '#'.$this->_requestUri['fragment'];
		}
		
		return($uri);
	}
}
