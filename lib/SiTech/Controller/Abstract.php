<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author eric
 */
abstract class SiTech_Controller_Abstract
{
	protected $_action;
	protected $_path;
	protected $_uri;

	public function __construct(SiTech_Uri $uri)
	{
		$this->_uri = $uri;
		
		$path = explode('/', $this->_uri->getPath(true));
		array_shift($path);
		$this->_action = array_shift($path);
		$this->_path = $path;

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
