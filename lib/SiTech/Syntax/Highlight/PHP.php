<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @see SiTech_Syntax_Highlight_Abstract
 */
require_once('SiTech/Syntax/Highlight/Abstract.php');

/**
 * Description of PHP
 *
 * @author Eric
 */
class SiTech_Syntax_Highlight_PHP extends SiTech_Syntax_Highlight_Abstract
{
	protected function _parseSource()
	{
		return(highlight_string($this->_source, true));
	}
}
