<?php
/**
 * Contains the PHP template renderer for template files.
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
 * SiTech_Template_Renderer_PHP - This renders files that are in PHP format into
 * complete output.
 *
 * @package SiTech_Template
 * @subpackage SiTech_Template_Renderer
 */
class SiTech_Template_Renderer_PHP extends SiTech_Template_Renderer_Abstract
{
	static public function render($file, $path, array $vars)
	{
		$_SiTech_oldPath = set_include_path($path.PATH_SEPARATOR.get_include_path());
		$fp = @fopen($file, 'r', true);
        if (!is_resource($fp)) {
			self::$error = 'Unable to read file '.$file.' on path '.$path;
			return(false);
		}
		extract($vars, EXTR_OVERWRITE);
		unset($vars);

		ob_start();
		include($file);
		$rendered = ob_get_clean();

		set_include_path($_SiTech_oldPath);
		return($rendered);
	}
}
