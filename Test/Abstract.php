<?php
set_include_path(get_include_path().PATH_SEPARATOR.realpath('../'));
require_once('SiTech.php');

abstract class Test_Abstract
{
	public function testMethod($method, $args)
	{
		if (isset($_SERVER['HTTP_HOST'])) echo '<pre>';
		
		try {
			$strArgs = $this->_implode($args);
			if (empty($strArgs)) {
				printf('  Test:%s  %s::%s()%s', "\n", get_class($this->_obj), $method, "\n");
			} else {
				printf('  Test:%s  %s::%s(\'%s\')%s', "\n", get_class($this->_obj), $method, $strArgs, "\n");
			}

			$return = call_user_func_array(array($this->_obj, $method), $args);
			echo '  SUCCESS!'."\n\n";
		} catch (Exception $ex) {
			echo '  FAILED!',"\n\n";
			echo $ex->getMessage(),"\n";
			echo $ex->getTraceAsString(),"\n\n";
		}

		if (isset($_SERVER['HTTP_HOST'])) echo '</pre>';

		return($return);
	}

	protected function _implode($array, $firstLevel = true)
	{
		if (is_array($array)) {
			foreach ($array as $key => $val) {
				$array[$key] = $this->_implode($val, false);
			}
		}

		if (!is_array($array)) {
			return($array);
		} else {
			if ($firstLevel) {
				return(implode('\', \'', $array));
			} else {
				return('Array(\''.implode('\', \'', $array).'\')');
			}
		}
	}
}
?>
