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
 * @todo Parse more stuff! We should have basic functionality here by parsing loops
 *       and conditionals. I don't want it to get too complex, but some base
 *       functionality would be nice.
 */
class SiTech_Template_Renderer_Macro extends SiTech_Template_Renderer_Abstract
{
	static public function render($file, $path, array $vars)
	{
		$rendered = file_get_contents($path.DIRECTORY_SEPARATOR.$file);
		if ($rendered === false) {
			self::$error = 'Unable to read file '.$file.' on path '.$path;
			return(false);
		}

		if (preg_match_all('#(\{\$([a-z][a-z0-9_]+)\})#im', $rendered, $variables)) {
			foreach($variables[2] as $k => $var) {
				if (isset($vars[$var])) {
					$rendered = str_replace($variables[1][$k], $vars[$var], $rendered);
				} else {
					$rendered = str_replace($variables[1][$k], '', $rendered);
					/* Hm, should we really trigger an error here? */
					trigger_error('Undefined variable: '.$var.' in template '.$path.DIRECTORY_SEPARATOR.$file.' code on line ??', E_USER_NOTICE);
				}
			}
		}

		return($rendered);
	}
}
