<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Highlight
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class SiTech_Syntax_Highlight
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
