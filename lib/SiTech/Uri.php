<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace SiTech;

/**
 * SiTech_Uri
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2009
 * @filesource
 * @package SiTech\Uri
 * @todo Finish documentation of SiTech_Uri class.
 * @version $Id$
 */
class Uri
{

	protected $_action;
	protected $_controller;
	protected $_requestUri;
	
	/**
	 * If no URI is passed to the constructor, this class will parse the current
	 * REQUEST_URI passed in by the web server.
	 *
	 * @param string $uri URI to parse
	 */
	public function __construct($uri = null)
	{
		if (\is_null($uri)) {
			if (defined('SITECH_BASEURI')) {
				$base = new Uri(\SITECH_BASEURI);
			}

			$host = \parse_url(((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')? 'https://' : 'http://').$_SERVER['HTTP_HOST']);
			$uri = $host['scheme'].'://'.$host['host'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		}

		$this->_requestUri = \parse_url($uri);
		if (!empty($this->_requestUri['query'])) {
			\parse_str($this->_requestUri['query'], $this->_requestUri['query']);
		} else {
			$this->_requestUri['query'] = array();
		}

		if (isset($base) && $base->getPath() != '/') {
			$this->_requestUri['rewritePath'] = \preg_replace('#^'.\preg_quote($base->getPath(), '#').'#', '', $this->_requestUri['path']);
			/* We need to have a value set for the rewrite path */
			if (empty($this->_requestUri['rewritePath'])) $this->_requestUri['rewritePath'] = '/';

			if (\strlen($this->_requestUri['rewritePath']) > 0 && $this->_requestUri['rewritePath'][0] != '/') {
				$this->_requestUri['rewritePath'] = '/'.$this->_requestUri['rewritePath'];
			}

			$parts = \explode('/', \ltrim($this->_requestUri['rewritePath'], '/'));
		} else {
			$parts = \explode('/', \ltrim($this->_requestUri['path'], '/'));
		}

		$this->_controller = (empty($parts[0]))? ((\defined('SITECH_DEFAULT_CONTROLLER'))? \SITECH_DEFAULT_CONTROLLER : 'default') : $parts[0];
		$this->_action = (empty($parts[1]))? 'index' : ((\is_int($parts[1]))? 'view' : $parts[1]);
	}

	public function __get($name)
	{
		$value = null;

		if (isset($this->_requestUri['query'][$name])) {
			$value = $this->_requestUri['query'][$name];
		}

		return($value);
	}

	public function __isset($name)
	{
		return((isset($this->_requestUri['query'][$name]))? true : false);
	}

	public function __set($name, $value)
	{
		$this->_requestUri['query'][$name] = $value;
	}

	/**
	 * Returns the string value of the URL.
	 */
	public function __toString()
	{
		return($this->getUri());
	}

	public function getAction($clean = false)
	{
		$ret = $this->_action;
		
		if ( $clean ) {
			$ret = \strtr($ret, '-', '_');
		}
		
		return($ret);
	}

	public function getController()
	{
		return($this->_controller);
	}

	public function getHost()
	{
		return((empty($this->_requestUri['host']))? $_SERVER['HOST_NAME'] : $this->_requestUri['host']);
	}

	public function getPath($flags = 0)
	{
		if ($flags & Uri\FLAG_REWRITE && isset($this->_requestUri['rewritePath'])) {
			$path = $this->_requestUri['rewritePath'];
		} else {
			$path = $this->_requestUri['path'];
		}

		if ($flags & Uri\FLAG_ACTION) {
			$path = \preg_replace('#^/'.$this->_controller.'/'.$this->_action.'#', '/'.$this->_controller, $path);
		}

		if ($flags & Uri\FLAG_CONTROLLER) {
			$path = \preg_replace('#^/('.$this->_controller.')#', '', $path);
		}

		if ($flags & Uri\FLAG_LTRIM) {
			$path = \ltrim($path, '/');
		}

		return($path);
	}

	public function getPort()
	{
		return($this->_requestUri['port']);
	}

	public function getQueryString()
	{
		return(\http_build_query($this->_requestUri['query']));
	}

	public function getScheme()
	{
		return((empty($this->_requestUri['scheme']))? 'http' : $this->_requestUri['scheme']);
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
			$uri .= '?'.$this->getQueryString();
		}

		if (!empty($this->_requestUri['fragment']) && $withFragment) {
			$uri .= '#'.$this->_requestUri['fragment'];
		}

		return($uri);
	}

	public function isRewrite()
	{
		return(empty($this->_requestUri['rewritePath'])? false : true);
	}

	public function internalRedirect($path)
	{
		if (!\defined('SITECH_BASEURI')) {
			throw new Uri\Exception('SITECH_BASEURI is not defined. Cannot redirect.');
		}

		$uri = \rtrim(\SITECH_BASEURI, '/');
		$path = \ltrim($path, '/');
		\header('Location: '.$uri.'/'.$path);
		exit;
	}

	public function setAction($action)
	{
		if (\parse_url($action) !== false) {
			$this->_action = $action;
			return($action);
		} else {
			return(false);
		}
	}

	public function setController($controller)
	{
		if (\parse_url($controller) !== false) {
			$this->_controller = $controller;
			return($controller);
		} else {
			return(false);
		}
	}

	public function setPath($path, $rewrite = false)
	{
		// Make sure the path is valid
		if (($url = \parse_url($path)) !== false) {
			if ($rewrite) {
				$this->_requestUri['rewritePath'] = $url['path'];
			} else {
				$this->_requestUri['path'] = $url['path'];
			}

			return($this->_requestUri['path']);
		} else {
			return(false);
		}
	}
}

namespace SiTech\Uri;

const FLAG_LTRIM = 1;
const FLAG_CONTROLLER = 2;
const FLAG_ACTION = 4;
const FLAG_REWRITE = 8;

require_once('Exception.php');
class Exception extends \SiTech\Exception {}
