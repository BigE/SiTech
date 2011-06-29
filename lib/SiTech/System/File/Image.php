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

namespace SiTech\System\File;

/**
 * Description of Image
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class Image extends \SplFileObject
{
	protected $_gd = false;
	protected $_imagick = false;

	public function __construct($file_name, $open_mode, $use_include_path, $context)
	{
		$this->_gd = extension_loaded('gd');
		$this->_imagick = extension_loaded('imagick');

		parent::SplFileObject($file_name, $open_mode, $use_include_path, $context);

		if ($this->_imagick) {
			$this->_imagick = new Imagick($file_name);
		}

		if ($this->_gd) {
			$info = getimagesize($file_name);
			switch ($info['mime']) {
				case 'image/gif':
					$this->_gd = ImageCreateFromGif($file_name);
					break;

				case 'image/jpg':
					$this->_gd = ImageCreateFromJpeg($file_name);
					break;

				default:
					break;
			}
		}
	}

	public function createThumbnail($width, $height, $path)
	{
	}

	public function resize($width, $height)
	{
	}
}
