<?php	
	session_name("aust");
	session_start();
	session_destroy();

	$st = '';
	if( !empty($_GET['status']))
		$st = '?status='.$_GET['status'];
	
	if($_GET['action'] == 'instalar'){
		if(is_file('instalar/install.php')){
			header("Location: instalar/install.php");
		} elseif(is_file('instalar/instalar2.php')){
			header("Location: instalar/instalar2.php");
		} else {
			header("Location: index.php".$st);
		}
	} else {
		header("Location: index.php".$st);
	}
	
?>