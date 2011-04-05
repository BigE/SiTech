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
require_once('SiTech/Template/Renderer/Base.php');

/**
 * This renders files that are in PHP format into complete output.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Template
 * @subpackage SiTech\Template\Renderer
 * @version $Id$
 */
class PHP extends Base
{
	static public function render(\SiTech\Template $tpl, $file, $path, array $vars)
	{
		$_SiTech_oldPath = \set_include_path($path.\PATH_SEPARATOR.\get_include_path());
		$fp = @\fopen($file, 'r', true);
        if (!\is_resource($fp)) {
			self::$error = 'Unable to read file '.$file.' on path '.$path;
			return(false);
		}
		\extract($vars, \EXTR_OVERWRITE);
		unset($vars);

		\ob_start();
		include($file);
		$content = \ob_get_clean();
		\ob_start();
		if ($tpl->getLayout() == null) {
			echo $content;
		} else {
			include(\SITECH_APP_PATH.'/layouts/'.$tpl->getLayout());
		}
		$rendered = \ob_get_clean();

		\set_include_path($_SiTech_oldPath);
		return($rendered);
	}
}
