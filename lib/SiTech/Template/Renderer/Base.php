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
 * Abstract class for rendering engines. All other renderer classes should
 * extend this class.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @abstract
 * @package SiTech\Template
 * @subpackage SiTech\Template\Renderer
 * @version $Id$
 */
abstract class Base implements IRenderer
{
	/**
	 * Error message that is set by the rendering engine.
	 *
	 * @var string
	 */
	static protected $error;

	/**
	 * Get and return a set error message from the renderer. If nothing is set
	 * the method will return null.
	 *
	 * @return string Error message that is set.
	 * @static
	 */
	static public function getError()
	{
		return(self::$error);
	}
}
