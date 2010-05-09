<?php
require 'tests/config/database.php';

if( !defined('UPLOAD_DIR') )
    define('UPLOAD_DIR', 'tests/test_files/uploaded_files/');

if( !defined('THIS_TO_BASEURL') )
    define('THIS_TO_BASEURL', '');

function __autoload($className) {
    if( is_file('core/class/'.$className.'.php') )
        require_once 'core/class/'.$className.'.php';
    else
        require_once 'core/class/'.$className.'.class.php';

}
?>
