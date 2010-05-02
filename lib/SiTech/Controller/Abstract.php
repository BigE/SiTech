<?php
/**
 * SiTech/Controller/Abstract.php
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
 * @copyright SiTech Group (c) 2008-2009
 * @filesource
 * @package SiTech_Controller
 * @version $Id$
 */

/**
 * Description of Abstract
 *
 * @package SiTech_Controller
 */
abstract class SiTech_Controller_Abstract
{
	protected $_action;
	protected $_argMap = array();
	protected $_args;

	/**
	 * This is a SiTech_Uri object used in the controller itself.
	 *
	 * @var SiTech_Uri
	 */
	protected $_uri;

	public function __construct(SiTech_Uri $uri)
	{
		$this->_uri = $uri;
		
		$this->_action = $this->_uri->getAction();

		$this->_args = explode('/', $this->_uri->getPath(SiTech_Uri::FLAG_LTRIM | SiTech_Uri::FLAG_CONTROLLER | SiTech_Uri::FLAG_ACTION));

		if (isset($this->_argMap[$this->_action]) && is_array($this->_argMap[$this->_action])) {
			foreach ($this->_argMap[$this->_action] as $k => $arg) {
				$this->_args[$arg] = $this->_args[$k];
			}
		}

		$this->init();
		if (!method_exists($this, $this->_action)) {
			throw new SiTech_Exception('Method not found', null, 404);
		}
		$this->{$this->_action}();
	}

	protected function init()
	{
	}
}
