<?php
if(!defined('THIS_TO_BASEURL')){
    define('THIS_TO_BASEURL', '');
}

if(!defined('CLASS_FILE_SUFIX')){
    define('CLASS_FILE_SUFIX', '.class');
}

function __autoload($class){
    if( is_file(CLASS_DIR.$class.".php") ){
        include_once(CLASS_DIR.$class.".php");
    } else if( is_file(CLASS_DIR.$class."".CLASS_FILE_SUFIX.".php") ){
        include_once(CLASS_DIR.$class."".CLASS_FILE_SUFIX.".php");
    }
}

/**
 * HELPERS
 */
include(CLASS_DIR."Helpers.php");
include(CLASS_DIR."helpers/EmbedFormsHelper.php");
include(CLASS_DIR."helpers/HtmlHelper.php");
?>