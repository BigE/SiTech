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

namespace SiTech\Syntax;

/**
 * Description of Highlight
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2009-2011
 * @filesource
 * @package SiTech_Syntax
 * @subpackage SiTech_Syntax_Highlight
 * @todo Finish file documentation.
 * @version $Id$
 */
class Highlight
{
	const TYPE_PHP = 1;

	static public function file($file, $type, $return = false)
	{
		switch ($type) {
			case self::TYPE_PHP:
				require_once('SiTech/Syntax/Highlight/PHP.php');
				$obj = new SiTech_Syntax_Highlight_PHP;
				break;

			default:
				throw new Exception('Invalid type');
				break;
		}

		$obj->loadFile($file);

		if ($return) {
			ob_start();
		}

		$obj->displaySource();

		if ($return) {
			$ret = ob_get_contents();
			ob_end_clean();
			return $ret;
		}
	}

	static public function string($string, $type, $return = false)
	{
	}
}
