<?php
// PHP 5.3 needs this
date_default_timezone_set('America/Sao_Paulo');

define('CONFIG_DATABASE_FILE', 'tests/config/database.php');

include_once CONFIG_DATABASE_FILE;
require("core/config/variables.php");

if( !defined('UPLOAD_DIR') )
    define('UPLOAD_DIR', 'tests/test_files/uploaded_files/');

if( !defined('THIS_TO_BASEURL') )
    define('THIS_TO_BASEURL', '');

define('TESTING', true);


require_once(CORE_DIR."libs/functions/func.php");
require_once(CORE_DIR."libs/functions/data_types.php");
require_once(CORE_DIR."libs/functions/string_treatment.php");

function autoload($className) {
	if( in_array($className, array("array","int", "integer","string","bool","float")) )
		return false;
    if( is_file('core/class/'.$className.'.php') )
        require 'core/class/'.$className.'.php';
    else
        require 'core/class/'.$className.'.class.php';

}

spl_autoload_register('autoload');

?>
