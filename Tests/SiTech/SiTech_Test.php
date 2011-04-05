<?php
define('SITECH_BASEPATH', realpath(dirname(__FILE__).'/../../'));
set_include_path('.'.PATH_SEPARATOR.SITECH_BASEPATH.'/lib');
// Turn scream off... this could cause problems with tests if we don't.
ini_set('xdebug.scream', 0);
?>
