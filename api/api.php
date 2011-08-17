<?php
/**
 * API Antenna
 *
 * @since v0.3, 15/07/2011
 */

if(!defined('THIS_TO_BASEURL')){
	define('THIS_TO_BASEURL', '../');
}

include_once(THIS_TO_BASEURL."core/config/variables.php");
include(CLASS_LOADER);

$application = new Api();
$application->dispatch($_GET);

?>