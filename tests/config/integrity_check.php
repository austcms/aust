<?php
// verifies and installs all tables automatically

$tables = Connection::getInstance()->query('show tables');
foreach( $tables as $table ){
	$table = reset($table);
	$sql = "DROP TABLE ". $table;
	Connection::getInstance()->exec($sql);
}

if( dbSchema::getInstance()->verificaSchema() <= 0 ){
	dbSchema::getInstance()->instalarSchema();
}
if( dbSchema::getInstance()->verificaSchema() <= 0 ){
	print "Tables not fully installed. There's some bug on installation.";
	exit();
}
?>