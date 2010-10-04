#!/usr/bin/php
<?php
/*
 * GETS THE VERSION OF GIT DESCRIBE
 *
 * Saves the actual version from git describe.
 */
if( file_exists('core/config/variables.php') ){
	define("THIS_TO_BASE_URL", "");
} elseif( file_exists('../core/config/variables.php') ){
	define("THIS_TO_BASE_URL", "../");
} else {
	print "Configuration File not found.";
	exit();
}

include_once(THIS_TO_BASE_URL. "core/config/variables.php");

$describe = exec("git describe");
if( empty($describe) ) exit(0);

// Takes the hash identifier off the end
$describe = substr($describe, 0, strlen($describe)-9);
$describe = str_replace("-", ".", $describe);

$file = fopen(THIS_TO_BASE_URL. VERSION_FILE, "w");
fwrite($file, $describe);
fclose($file);

print "Version updated successfully.\n"
?>