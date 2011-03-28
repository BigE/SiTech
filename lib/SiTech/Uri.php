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
 *
 * @package SiTech\Uri
 */

namespace SiTech;

/**
 * SiTech_Uri
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2009-2011
 * @filesource
 * @package SiTech\Uri
 * @todo Finish documentation of SiTech_Uri class.
 * @version $Id$
 */
class Uri
{

	protected $_format;
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
			$host = \parse_url(((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')? 'https://' : 'http://').$_SERVER['HTTP_HOST']);
			$uri = $host['scheme'].'://'.$host['host'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		}

		$this->_requestUri = \parse_url($uri);
		if (!empty($this->_requestUri['query'])) {
			\parse_str($this->_requestUri['query'], $this->_requestUri['query']);
		} else {
			$this->_requestUri['query'] = array();
		}

		if (($fPos = \strrpos($this->_requestUri['path'], '.')) !== false) {
			$this->_format = \substr($this->_requestUri['path'], $fPos);
		}
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

	public function getFormat()
	{
		return($this->_format);
	}

	public function getHost()
	{
		return((empty($this->_requestUri['host']))? $_SERVER['HOST_NAME'] : $this->_requestUri['host']);
	}

	public function getPath($flags = null)
	{
		$path = $this->_requestUri['path'];

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

	public function internalRedirect($path)
	{
		$path = '/'.\ltrim($path, '/');
		if (\defined('SITECH_PATH_PREFIX')) $path = \SITECH_PATH_PREFIX.$path;
		\header('Location: '.$path);
		exit;
	}
}

namespace SiTech\Uri;

const FLAG_LTRIM = 1;

require_once('Exception.php');
class Exception extends \SiTech\Exception {}
