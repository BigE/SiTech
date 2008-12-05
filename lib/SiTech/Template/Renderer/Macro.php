<?php
/**
 * Contains the Smarty style template renderer for template files.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_Template
 * @subpackage SiTech_Template_Renderer
 * @version $Id: PHP.php 146 2008-12-03 07:33:49Z eric $
 */

/**
 * @see SiTech_Template_Renderer_Abstract
 */
require_once('SiTech/Template/Renderer/Abstract.php');

/**
 * SiTech_Template_Renderer_Macro - This renders files that are in Smarty syntax
 * into complete output. This does not provide the full support of Smarty just
 * simply uses the syntax.
 *
 * @package SiTech_Template
 * @subpackage SiTech_Template_Renderer
 */
class SiTech_Template_Renderer_Macro extends SiTech_Template_Renderer_Abstract
{
	static protected $template;

	static public function render($file, $path, array $vars)
	{
		self::$template = file_get_contents($path.DIRECTORY_SEPARATOR.$file);
		if (self::$template === false) {
			self::$error = 'Unable to read file '.$file.' on path '.$path;
			return(false);
		}

		if (preg_match_all('', self::$template, $matches)) {
		}

		return($rendered);
	}
}
