#!/usr/bin/php
<?php
/*
 * GETS THE SOFTWARE VERSION
 */
if( file_exists('core/config/variables.php') ){
	define("THIS_TO_BASE_URL", "");
} elseif( file_exists('../core/config/variables.php') ){
	define("THIS_TO_BASE_URL", "../");
} else {
	print "Configuration File not found.\n";
	exit();
}

include_once(THIS_TO_BASE_URL. "core/config/variables.php");

if( !file_exists(THIS_TO_BASE_URL. VERSION_FILE) ){
	print "Version file not found.\n";
	exit();
}

$version = file_get_contents(THIS_TO_BASE_URL. VERSION_FILE);
if( empty($version) )
	print "No version set.\n";
else
	print $version."\n";
?>