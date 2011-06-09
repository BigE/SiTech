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

namespace SiTech\HTTP;

/**
 * Description of Response
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class Response
{
	protected $_body;
	
	protected $_code;

	protected $_headers = array();
	
	protected $_message;

	/**
	 * A list of response codes 
	 *
	 * @see http://www.ietf.org/rfc/rfc2616.txt http://www.ietf.org/rfc/rfc1945.txt
	 * @var array
	 */
	protected static $_codes = array(
		// Informational 1xx
		100 => array('1.1' => 'Continue'),
		101 => array('1.1' => 'Switching Protocols'),
		// Successful 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => array('1.1' => 'Non-Authoritaive Information'),
		204 => 'No Content',
		205 => array('1.1' => 'Reset Content'),
		206 => array('1.1' => 'Partial Content'),
		// Redirection 3xx
		300 => array('1.1' => 'Multiple Choices'),
		301 => 'Moved Permanently',
		302 => array('1.1' => 'Found', '1.0' => 'Moved Temporarily'),
		303 => array('1.1' => 'See Other'),
		304 => 'Not Modified',
		305 => array('1.1' => 'Use Proxy'),
		307 => array('1.1' => 'Temporary Redirect'),
		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => array('1.1' => 'Payment Required'),
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => array('1.1' => 'Method Not Allowed'),
		406 => array('1.1' => 'Not Acceptable'),
		407 => array('1.1' => 'Proxy Authentication Required'),
		408 => array('1.1' => 'Request Timeout'),
		409 => array('1.1' => 'Conflict'),
		410 => array('1.1' => 'Gone'),
		411 => array('1.1' => 'Length Required'),
		412 => array('1.1' => 'Precondition Failed'),
		413 => array('1.1' => 'Request Entity Too Large'),
		414 => array('1.1' => 'Request-URI Too Long'),
		415 => array('1.1' => 'Unsupported Media Type'),
		416 => array('1.1' => 'Requested Range Not Satisfiable'),
		417 => array('1.1' => 'Expectation Failed'),
		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => array('1.1' => 'Gateway Timeout'),
		505 => array('1.1' => 'HTTP Version Not Supported')
	);

	protected $_version;

	public function __construct($code, array $headers = array(), $body = null, $version = '1.1', $message = null)
	{
		$this->_code = $code;
		$this->_body = $body;
		$this->_message = (empty($message))? self::responseCodeAsText($code, $version) : $message;

		foreach ($headers as $k => $v) {
			if (!is_string($k)) {
				$header = explode(':', $v, 2);
				if (count($header) != 2) {
					require_once('SiTech/HTTP/Response/Exception.php');
					throw new Response\InvalidHeaderException('Invalid HTTP header specified: %s', array($v));
				}

				$k = $header[0];
				$v = $header[1];
			}

			$this->_headers[strtolower($k)] = $v;
		}
		
		if (!preg_match('#^[0-9]\.[0-9]$#', $version)) {
			throw new Response\InvalidVersionException('Invalid HTTP version specified: %s', array($version));
		}

		$this->_version = $version;
	}

	public function __toString()
	{
		return($this->getHeadersAsString()."\n\n".$this->getBody());
	}

	public static function fromString($response)
	{
		preg_match("#^HTTP/([0-9]\.[0-9]) ([0-9]+) ([^\r?\n]+)\r?\n(.*)[\r?\n]{2}(.*)#s", $response, $m);

		return(new Response($m[2], preg_split("#[\r?\n]#", $m[4]), $m[5], $m[1], $m[3]));
	}

	public function getBody($raw = true)
	{
		$body = $this->_body;

		if ($body instanceof \SplFileInfo) {
			$body = file_get_contents($body->getRealPath());
		}
		
		if (!$raw) {
		}

		return($body);
	}

	public function getCode()
	{
		return($this->_code);
	}

	public function getHeader($name)
	{
		return((isset($this->_headers[strtolower($name)]))? $this->_headers[strtolower($name)] : null);
	}

	public function getHeaders()
	{
		return($this->_headers);
	}

	public function getHeadersAsString($sep = "\n")
	{
		$headers = '';
		
		foreach ($this->_headers as $name => $value) {
			$headers .= ucfirst($name).':'.$value.$sep;
		}

		return($headers);
	}

	public function getMessage()
	{
		return($this->_message);
	}

	public function getVersion()
	{
		return($this->_version);
	}

	/**
	 * Take everything that's been set so far and send it. This will
	 * automatically send all header() commands needed.
	 */
	public function output()
	{
		$body = $this->getBody();
		header('HTTP/'.$this->_version.' '.$this->_code.' '.$this->_message);
		
		foreach ($this->_headers as $name => $value) {
			if ($name == 'content-encoding') {
				switch ($value) {
					case 'gzip':
						$body = gzencode($body);
						break;

					case 'defalte':
						$body = gzcompress($body);
						break;
				}
			}
			header($name.':'.$value);
		}

		if (!isset($this->_headers['content-length'])) {
			header('Content-Length: '.strlen($body));
		}
		
		echo $body;
	}

	public static function responseCodeAsText($code, $version = '1.1')
	{
		if (isset(self::$_codes[$code]) && !is_array(self::$_codes[$code])) {
			// We don't care what HTTP version it is because its the same in 1.0 and 1.1
			return(self::$_codes[$code]);
		} elseif (isset(self::$_codes[$code]) && is_array(self::$_codes[$code]) && isset(self::$_codes[$code][$version])) {
			return(self::$_codes[$code][$version]);
		} else {
			return('Unknown');
		}
	}
}
