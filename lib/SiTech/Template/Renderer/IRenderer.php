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
 * Interface for all rendering enginges.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Template
 * @subpackage SiTech\Template\Renderer
 * @version $Id$
 */
interface IRenderer
{
	/**
	 * Render the template and output the result.
	 *
	 * @param \SiTech\Template\Engine $tpl Template engine object so the renderer
	 *                                     can get the needed info
	 * @param string $file Filename of the template.
	 * @param string $path Template base path.
	 * @param array $vars Array of template variables to be used in the template.
	 * @return string Returns a string of the rendered template.
	 */
	static public function render(\SiTech\Template\Engine $tpl, $file, $path, array $vars);
}
