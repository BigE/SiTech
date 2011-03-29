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
 *
 * @filesource
 */

namespace SiTech\Template\Renderer;

/**
 * @see SiTech\Template\Renderer\ARenderer
 */
require_once('SiTech/Template/Renderer/ARenderer.php');

/**
 * This renders files that are in Smarty syntax into complete output. This does
 * not provide the full support of Smarty just simply uses similar syntax.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Template
 * @subpackage SiTech\Template\Renderer
 * @todo Parse more stuff! We should have basic functionality here by parsing loops
 *       and conditionals. I don't want it to get too complex, but some base
 *       functionality would be nice.
 * @version $Id$
 */
class Macro extends ARenderer
{
	static public function render(\SiTech\Template $tpl, $file, $path, array $vars)
	{
		$rendered = \file_get_contents($path.\DIRECTORY_SEPARATOR.$file);
		if ($rendered === false) {
			self::$error = 'Unable to read file '.$file.' on path '.$path;
			return(false);
		}

		if (\preg_match_all('#(\{\$([a-z][a-z0-9_]+)\})#im', $rendered, $variables)) {
			foreach($variables[2] as $k => $var) {
				if (isset($vars[$var])) {
					$rendered = \str_replace($variables[1][$k], $vars[$var], $rendered);
				} else {
					$rendered = \str_replace($variables[1][$k], '', $rendered);
					/* Hm, should we really trigger an error here? */
					\trigger_error('Undefined variable: '.$var.' in template '.$path.\DIRECTORY_SEPARATOR.$file.' code on line ??', \E_USER_NOTICE);
				}
			}
		}

		return($rendered);
	}
}
