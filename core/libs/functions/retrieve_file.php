<?php
if( empty($_GET['path']) ||
 	empty($_GET['type']) ||
 	empty($_GET['filename']) ){
	echo 'missing arguments';
	exit();
}

$path = $_GET['path'];
$type = $_GET['type'];
$filename = $_GET['filename'];

// We'll be outputting a PDF
header('Content-type: '.$type);

// It will be called downloaded.pdf
header('Content-Disposition: attachment; filename="'.$filename.'"');

// The PDF source is in original.pdf
readfile($path);

//file_get_contents($path);
?>