<?php
// verifies and installs all tables automatically
if( dbSchema::getInstance()->verificaSchema() <= 0 ){
	dbSchema::getInstance()->instalarSchema();
}
if( dbSchema::getInstance()->verificaSchema() <= 0 ){
	print "Tables not fully installed. There's some bug on installation.";
	exit();
}
?>