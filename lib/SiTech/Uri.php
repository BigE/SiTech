<?php
/**
 * SiTech/Uri.php
 *
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
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2009
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Uri
 * @todo Finish documentation of SiTech_Uri class.
 * @version $Id$
 */

/**
 * SiTech_Uri
 *
 * @package SiTech_Uri
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

	public function getHost()
	{
		return((empty($this->_requestUri['host']))? $_SERVER['HOST_NAME'] : $this->_requestUri['host']);
	}

	public function getPath($ltrim = false)
	{
		if ($ltrim) {
			return(ltrim($this->_requestUri['path'], '/'));
		} else {
			return($this->_requestUri['path']);
		}
	}

	public function getPort()
	{
		return($this->_requestUri['port']);
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
			$uri .= '?'.$this->_requestUri['query'];
		}

		if (!empty($this->_requestUri['fragment']) && $withFragment) {
			$uri .= '#'.$this->_requestUri['fragment'];
		}
		
		return($uri);
	}
}
