<?php
/*
 * Where everything starts.
 *
 * @since 25/07/2009
 */
/*
 * Create Session
 */

session_name("aust");
session_start();

/*
 * Defines the path to root (so we can load files)
 */
if(!defined('THIS_TO_BASEURL')){
	define('THIS_TO_BASEURL', '');
}

include_once(THIS_TO_BASEURL."core/config/variables.php");

include(CLASS_LOADER);

$application = new Application();
?>