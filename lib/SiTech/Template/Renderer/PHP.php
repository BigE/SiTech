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
 * @see SiTech\Template\Renderer\Base
 */
require_once('SiTech/Template/Renderer/IRenderer.php');

/**
 * @see SiTech\Template\Renderer\Exception
 */
require_once('SiTech/Template/Renderer/Exception.php');

/**
 * This renders files that are in PHP format into complete output.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Template
 * @subpackage SiTech\Template\Renderer
 * @version $Id$
 */
class PHP implements IRenderer
{
	/**
	 * Render the template file and then return the output. Since we're using
	 * PHP to render our template, all variables are extracted to the space of
	 * the template. The template is then loaded using include() and the
	 * output captured by the output buffer functions.
	 *
	 * @param SiTech\Template\Engine $tpl Main instance of the template engine.
	 * @param string $file Template file to render
	 * @param string $path Path of where we find the template at
	 * @param array $vars Variables that are set for the template
	 * @return string Final output of template after rendered
	 */
	static public function render(\SiTech\Template\Engine $tpl, $file, $path, array $vars)
	{
		// Backup the old include path so we can reset it once we're done.
		$_SiTech_oldPath = \set_include_path($path.\PATH_SEPARATOR.\get_include_path());
		\extract($vars, \EXTR_OVERWRITE);
		unset($vars);

		\ob_start();
		@include($file);
		$error = get_last_error();
		if (!empty($error) && $error['type'] == E_WARNING && preg_match('#include\(\): Failed opening \''.$file.'\'#')) {
			\ob_end_clean();
			throw new Exception('Failed to open template file %s on path %s', array($file, $path));
		}
		$content = \ob_get_clean();
		\ob_start();
		if ($tpl->getLayout() == null) {
			echo $content;
		} else {
			include($tpl->getLayout());
		}
		$rendered = \ob_get_clean();

		\set_include_path($_SiTech_oldPath);
		return($rendered);
	}
}
