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
 * @see SiTech\Template\Renderer\IRenderer
 */
require_once('SiTech/Template/Renderer/IRenderer.php');

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
class Macro implements IRenderer
{
	/**
	 * Here we render the Macro template format. Currently the only supported
	 * parts of the macro template format are variables. There are no control
	 * or conditional structures supported yet.
	 *
	 * @param \SiTech\Template\Engine $tpl Instance of the template engine
	 * @param type $file Template file to parse and render
	 * @param type $path Path of where to find the template
	 * @param array $vars Variables that are to be used in the template
	 * @return string Final output of the template after its rendered
	 */
	static public function render(\SiTech\Template $tpl, $file, $path, array $vars)
	{
		$rendered = \file_get_contents($path.\DIRECTORY_SEPARATOR.$file);
		if ($rendered === false) {
			throw new Exception('Unable to read file %s on path %s', array($file, $path));
		}

		if (\preg_match_all('#(\{\$([a-z][a-z0-9_]+)\})#im', $rendered, $variables)) {
			foreach($variables[2] as $k => $var) {
				if (isset($vars[$var])) {
					$rendered = \str_replace($variables[1][$k], $vars[$var], $rendered);
				} else {
					$rendered = \str_replace($variables[1][$k], '', $rendered);
					// Trigger a E_USER_NOTICE here. This is hidden by the template
					// engine unless strict mode is turned on.
					\trigger_error('Undefined variable: '.$var.' in template '.$path.\DIRECTORY_SEPARATOR.$file.' code on line ??', \E_USER_NOTICE);
				}
			}
		}

		return($rendered);
	}
}
