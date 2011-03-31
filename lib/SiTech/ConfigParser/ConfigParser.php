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

namespace SiTech\ConfigParser;

require_once('SiTech/ConfigParser/RawConfigParser.php');

/**
 * This config parser supports interpolation with the variables coming from the
 * configuration.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\ConfigParser
 * @todo Finish documentation of how interpolation works.
 * @version $Id$
 */
class ConfigParser extends RawConfigParser
{
	/**
	 * Get a value from the configuration and interpolate any variables that are
	 * in the value before returning it.
	 *
	 * @param string $section
	 * @param string $option
	 * @return mixed
	 */
	public function get($section, $option)
	{
		$value = parent::get($section, $option);
		if (preg_match_all('#(%(\(((([a-z]+):)?([a-z]+))\))([^\s]+)?[bcdeEufFgGosxX])#', $value, $matches)) {
			$len = sizeof($matches[0]);
			$replace = array();
			for ($x = 0; $x < $len; $x++) {
				if (empty($matches[5][$x])) {
					$matches[5][$x] = $section;
				}

				$replace[] = $this->get($matches[5][$x], $matches[6][$x]);
				$value = str_replace($matches[2][$x], '', $value);
			}
			$value = vsprintf($value, $replace);
		}
		return($value);
	}
}
