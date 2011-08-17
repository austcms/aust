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
	} else if( is_file(API_CLASS_DIR.$class.".php") ){
		include_once(API_CLASS_DIR.$class.".php");
	}
}

/**
 * HELPERS
 */
include(CLASS_DIR."Helpers.php");
include(CLASS_DIR."helpers/HtmlHelper.php");
?>