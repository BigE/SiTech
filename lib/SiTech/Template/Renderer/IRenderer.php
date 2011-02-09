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
 */

namespace SiTech\Template\Renderer;

/**
 * Interface for all rendering enginges.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2011
 * @filesource
 * @package SiTech\Template
 * @subpackage SiTech\Template\Renderer
 * @version $Id$
 */
interface IRenderer
{
	/**
	 * Return an error string if one exists.
	 *
	 * @return string
	 */
	static public function getError();

	/**
	 * Render the template and output the result.
	 *
	 * @param string $file Filename of the template.
	 * @param string $path Template base path.
	 * @param array $vars Array of template variables to be used in the template.
	 * @return string Returns FALSE on failure.
	 */
	static public function render(\SiTech\Template $tpl, $file, $path, array $vars);
}

require_once('SiTech/Template.php');
class Exception extends \SiTech\Template\Exception {}
