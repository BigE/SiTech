<?php
set_include_path(get_include_path().PATH_SEPARATOR.realpath('../'));
require_once('SiTech.php');

/* Basic test for a file that doesn't exist */
try {
    SiTech::loadClass('Test_Class');
} catch (Exception $ex) {
    echo $ex->getMessage()."<br />\n<br />\n";
    echo nl2br($ex->getTraceAsString());
}
?>
