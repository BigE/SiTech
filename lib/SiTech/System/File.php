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

namespace SiTech\System;

/**
 * @todo Remove this require once the MIME capabilities are built.
 */
require_once('SiTech/Exception.php');

/**
 * File is just an extension of SplFileInfo that adds some basic file checks
 * and manipulation.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\System
 * @version $Id$
 */
class File extends \SplFileInfo
{
	/**
	 * Replacement method for the method that was implemented in 5.3.6. This is
	 * to enable support on older 5.3 releases of PHP.
	 *
	 * @return string
	 */
	public function getExtension()
	{
		if (method_exists('SplFileInfo', 'getExtension'))
				return(parent::getExtension());

		return(ltrim(strrchr($this->getFilename(), '.'), '.'));
	}

	/**
	 * Check if the file selected is an image or not. Currently no checking is
	 * done besides the file extension. MIME type checking will be implemented
	 * soon.
	 *
	 * @param bool $strict If set to true, perform a MIME check against the file.
	 * @return bool
	 * @todo Add a optional strict MIME check against the file
	 */
	public function isImage($strict = false)
	{
		if ($strict) throw new SiTech\NotImplementedException ('Strict checking against MIME type is not supported yet');
		$ext = $this->getExtension();
		return(in_array($ext, array('jpg', 'jpeg', 'gif', 'tiff', 'png', 'bmp')));
	}

	/**
	 * Check if the file selected is a video or not. Currently no checking is
	 * done besides the file extension. MIME type checking will be implemented
	 * soon.
	 *
	 * @param bool $strict If set to true, perform a MIME check against the file.
	 * @return bool
	 * @todo Add a optional strict MIME check against the file
	 */
	public function isVideo($strict = false)
	{
		if ($strict) throw new SiTech\NotImplementedException ('Strict checking against MIME type is not supported yet');
		$ext = $this->getExtension();
		return(in_array($ext, array('mpg', 'mpeg', 'avi', 'flv', 'wmv')));
	}

	/**
	 * This overrides the built in function. If the file is a type supported by
	 * SiTech, it will automatically call ::setFileClass() with the proper class
	 * name to use when opening the file for extra options and support.
	 *
	 * @param string $open_mode Mode for opening the file.
	 * @param bool $use_include_path If true, the include_path is also searched.
	 * @param string $context
	 */
	public function openFile($open_mode = 'r', $use_include_path = false, $context = null)
	{
		if ($this->isImage()) {
			require_once('SiTech/System/File/Image.php');
			$this->setFileClass('\SiTech\System\File\Image');
		}

		$obj = parent::openFile($open_mode, $use_include_path, $context);
		$this->setFileClass('\SplFileObject');
		return($obj);
	}
}
