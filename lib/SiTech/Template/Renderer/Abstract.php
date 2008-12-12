<?php
/**
 * Contains the abstract renderer for template files.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_Template
 * @subpackage SiTech_Template_Renderer
 * @version $Id: Abstract.php 98 2008-06-29 17:32:06Z eric $
 */

/**
 * @see SiTech_Template_Renderer_Interface
 */
require_once('SiTech/Template/Renderer/Interface.php');

/**
 * SiTech_Template_Renderer_Abstract - Abstract class for rendering engines. All
 * other renderer classes should extend this class.
 *
 * @package SiTech_Template
 * @subpackage SiTech_Template_Renderer
 */
abstract class SiTech_Template_Renderer_Abstract implements SiTech_Template_Renderer_Interface
{
	static protected $error;

	static public function getError()
	{
		return(self::$error);
	}
}
