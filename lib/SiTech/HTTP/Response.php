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
 * Build or parse a full HTTP response. This class enables easier processing or
 * creation of full HTTP responses.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\HTTP
 * @todo Add gzip/deflate compression support.
 * @version $Id$
 */
class Response
{
	/**
	 * This is the full body of the HTTP response. This can be just about
	 * anything depending on what is being served up.
	 *
	 * @see getBody
	 * @var string
	 */
	protected $_body;

	/**
	 * This is the HTTP code that is sent with the request.
	 *
	 * @see $_codes getCode
	 * @var int
	 */
	protected $_code;

	/**
	 * These are the HTTP headers that are sent with the response. Each header
	 * is stored as an element in the array using header_name => value
	 *
	 * @see getHeader getHeaders getHeadersAsString
	 * @var array
	 */
	protected $_headers = array();

	/**
	 * This is the message that is sent with the HTTP code.
	 *
	 * @see getMessage
	 * @var string
	 */
	protected $_message;

	/**
	 * A list of response codes
	 *
	 * @see responseCodeAsText http://www.ietf.org/rfc/rfc2616.txt http://www.ietf.org/rfc/rfc1945.txt
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

	/**
	 * This is the HTTP version we are using. Currently only 1.0 and 1.1 are
	 * availble according to the HTTP RFC
	 *
	 * @see getVersion
	 * @var string
	 */
	protected $_version;

	/**
	 * Here is where we create a new Response object. The only thing that must
	 * be specified is the HTTP code. If no message is specified, the default
	 * message will be pulled from the codes array.
	 *
	 * @param int $code HTTP code to use with the response.
	 * @param array $headers Array of headers to send with the response.
	 * @param string $body Content to send with the response.
	 * @param string $version The HTTP version to use for the response.
	 * @param string $message Message to use with the code other than the default.
	 * @see $_codes
	 */
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

	/**
	 * Parse the full response and return it as a string. This will include all
	 * headers and the body as well as the HTTP version, code, and message.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return($this->getHeadersAsString()."\n\n".$this->getBody());
	}

	/**
	 * This takes a full HTTP Response from a string and creates a new object
	 * from it. It will parse out the HTTP code, message, the headers and the
	 * document body.
	 *
	 * @param string $response
	 * @return SiTech\HTTP\Response
	 */
	public static function fromString($response)
	{
		preg_match("#^HTTP/([0-9]\.[0-9]) ([0-9]+) ([^\r?\n]+)\r?\n(.*)[\r?\n]{2}(.*)#s", $response, $m);

		return(new Response($m[2], preg_split("#[\r?\n]#", $m[4]), $m[5], $m[1], $m[3]));
	}

	/**
	 * Get the body (content) of the current response stored in the object. The
	 * raw flag tells the method if it should decode the content before
	 * returning it.
	 *
	 * @param bool $raw If false the body will be decoded before it is returned.
	 * @return string
	 */
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

	/**
	 * Return the HTTP code used with the response.
	 *
	 * @return int
	 */
	public function getCode()
	{
		return((int)$this->_code);
	}

	/**
	 * Get the specified header from the response and return the value. If the
	 * header specified was not set in the response then false will be returned.
	 *
	 * @param string $name Header name to get the value for.
	 * @return string
	 */
	public function getHeader($name)
	{
		return((isset($this->_headers[strtolower($name)]))? $this->_headers[strtolower($name)] : false);
	}

	/**
	 * Return an array of all the headers stored in the HTTP response. The array
	 * will be returned with "header_name => value" format for each header.
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return($this->_headers);
	}

	/**
	 * Return a string of all the headers that are in the response. If a
	 * separator is defined, it will be used instead of the default \r\n that
	 * is used in HTTP responses.
	 *
	 * @param string $sep
	 * @return string
	 */
	public function getHeadersAsString($sep = "\r\n")
	{
		$headers = '';

		foreach ($this->_headers as $name => $value) {
			$headers .= ucfirst($name).':'.$value.$sep;
		}

		return($headers);
	}

	/**
	 * Get the message that was sent with the HTTP response. The message is
	 * passed in on the same line as the version and code.
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return($this->_message);
	}

	/**
	 * Return the HTTP version used with the response.
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return($this->_version);
	}

	/**
	 * Render the entier response and send it. This will loop through all the
	 * currently set headers and call the header() function in PHP respectively.
	 * Essentially this will output the full HTTP response to the client and
	 * should end output completely from the script.
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

			header(str_replace(array(' ', '[s]'), array('-', ' '), ucwords(str_replace(array(' ', '-'), array('[s]', ' '), $name))).':'.$value);
		}

		if (!isset($this->_headers['content-length'])) {
			header('Content-Length: '.strlen($body));
		}

		echo $body;
	}

	/**
	 * Take a HTTP response code and translate it to the default text assigned
	 * to it. If the code is not found in the version specified, the method
	 * will return 'Unknown' as the message.
	 *
	 * @param int $code HTTP code to be translated to a string.
	 * @param string $version HTTP version to be used when translating.
	 * @return string
	 */
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
