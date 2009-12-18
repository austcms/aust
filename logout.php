<?    
	session_name("aust");
	session_start();
	session_destroy();
    if($_GET['action'] == 'instalar'){
        if(is_file('instalar/instalar.php')){
        	header("Location: instalar/instalar.php");
        } elseif(is_file('instalar/instalar2.php')){
            header("Location: instalar/instalar2.php");
        } else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }
    
?>

