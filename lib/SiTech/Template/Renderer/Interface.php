<?php
/**
 * Contains the interface for all template renderer engines.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech_Template
 * @subpackage SiTech_Template_Renderer
 * @version $Id$
 */

/**
 * SiTech_Template_Renderer_Interface - Interface for all rendering enginges.
 *
 * @package SiTech_Template
 * @subpackage SiTech_Template_Renderer
 */
interface SiTech_Template_Renderer_Interface
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
	static public function render($file, $path, array $vars);
}
