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
 * The URI class takes a URI into the constructor and parses it up into peices
 * that can be utilized using its different methods. If no URI is passed into
 * the constructor it is guessed using specific $_SERVER variables. This can be
 * used to build urls to be used within the website. It should support
 * everything that parse_url supports.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech
 * @see parse_url
 * @version $Id$
 */
class Uri
{
	/**
	 * This is the format that the URI came in as. This means the file extension
	 * specified by the URI.
	 *
	 * @var string
	 */
	protected $_format;

	/**
	 * After parse_url is run, the parts are stored here for access by the rest
	 * of the class.
	 *
	 * @var array
	 */
	protected $_requestUri;

	/**
	 * If no URI is passed to the constructor, this class will parse the current
	 * REQUEST_URI passed in by the web server. It also does https detection and
	 * uses HTTP_HOST and SERVER_PORT from $_SERVER to detect a better URI.
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

	/**
	 * This is the magic get method. Here we pull from the query string that was
	 * passed in the URI.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		$value = null;

		if (isset($this->_requestUri['query'][$name])) {
			$value = $this->_requestUri['query'][$name];
		}

		return($value);
	}

	/**
	 * Magic isset method. This helps the magic get method and any direct calls
	 * to variables using isset, check to see if a variable is set in the query
	 * string.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return((isset($this->_requestUri['query'][$name]))? true : false);
	}

	/**
	 * Magic set method to set variables in the query string. This is useful for
	 * building a URI to output. The value will use urlencode() to make it safe
	 * for output. Objects and arrays will also be serialized.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		if (is_object($value) || is_array($value)) $value = serialize($value);
		$this->_requestUri['query'][$name] = urlencode($value);
	}

	/**
	 * Magic method to output the URI in full when the object is used as a
	 * string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return($this->getUri());
	}

	/**
	 * Pull the file extension and return it. This is considered the format so
	 * it can be detected what format we should output.
	 *
	 * @return string
	 */
	public function getFormat()
	{
		return($this->_format);
	}

	/**
	 * This is the host that was passed in through the URI in the constructor. If
	 * no host was parsed out, we substitue using the HOST_NAME variable.
	 *
	 * @return string
	 */
	public function getHost()
	{
		return((empty($this->_requestUri['host']))? $_SERVER['HOST_NAME'] : $this->_requestUri['host']);
	}

	/**
	 * Get the path of the URI that was passed in through the constructor. This
	 * method takes the FLAG_LTRIM as an argument to trim the beginning / off of
	 * the path.
	 *
	 * @param int $flags
	 * @return string
	 */
	public function getPath($flags = null)
	{
		$path = $this->_requestUri['path'];

		if ($flags & Uri\FLAG_LTRIM) {
			$path = \ltrim($path, '/');
		}

		return($path);
	}

	/**
	 * Return the port portion of the URI passed into the constructor.
	 *
	 * @return int
	 */
	public function getPort()
	{
		return((int)$this->_requestUri['port']);
	}

	/**
	 * This takes all the variables set to the class in the query string and 
	 * builds a string that can be used for the query string.
	 *
	 * @return string
	 */
	public function getQueryString()
	{
		return(\http_build_query($this->_requestUri['query']));
	}

	/**
	 * Get the scheme from the URI passed in. If no scheme is found http is
	 * assumed.
	 *
	 * @return string
	 */
	public function getScheme()
	{
		return((empty($this->_requestUri['scheme']))? 'http' : $this->_requestUri['scheme']);
	}

	/**
	 * This pulls the full uri from the class. If the $withQuery variable is
	 * set to false, the query string won't be sent with the URI. If the
	 * $withFragment is set to false the fragment won't be included with the
	 * URI.
	 *
	 * @param bool $withQuery
	 * @param bool $withFragment
	 * @return string
	 */
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

	/**
	 * This is an internal redirect for the class. If the SITECH_PATH_PREFIX
	 * is specified, it is prepended to the beginning of the path for the
	 * redirect.
	 *
	 * @param string $path
	 */
	public function internalRedirect($path)
	{
		$path = '/'.\ltrim($path, '/');
		if (\defined('SITECH_PATH_PREFIX')) $path = \SITECH_PATH_PREFIX.$path;
		\header('Location: '.$path);
		exit;
	}
}

namespace SiTech\Uri;

/**
 * If passed into the getPath method, it will trim the leading / off the path.
 */
const FLAG_LTRIM = 1;

require_once('Exception.php');

/**
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Uri
 * @version $Id$
 */
class Exception extends \SiTech\Exception {}
