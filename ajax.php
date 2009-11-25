<?php
function PegaData($formato){
	$formato = StrToLower($formato);
	if ($formato == "dia") return date("d");
	if ($formato == "mes") return date("m");
	if ($formato == "ano") return date("Y");
	if ($formato == "hora") return date("H");
	if ($formato == "minuto") return date("i");
	if ($formato == "segundo") return date("s");
}

    $islocalhost = true;

    if($islocalhost){
        $dbname = "aust1";
        $usuario = "root"; 
        $password = "2104"; 

        $con = mysql_connect("192.168.2.108", $usuario);
    } else {
        $dbname = "razaoaurea"; 
        $usuario = "razaoaurea"; 
        $password = "mysql2104"; 

        $con = mysql_connect("mysql01.razaoaurea.com.br", $usuario, $password);
    }

    mysql_select_db($dbname, $con);

    switch($_POST['acao']){
        case "verifyifexists" : {
			switch($_POST['dbtable']){
				case "adm" : 
					$table = "admins";
					break;
				case "con" : 
					$table = "content";
					break;
			
			}
			$sql = "SELECT id
					FROM $table
					WHERE ".$_POST['campo']."='".$_POST['valor']."'";
			$mysql = mysql_query($sql);
			$xml = mysql_num_rows($mysql);
		
			break;
		}
        case "subordinado" : {
            header("Content-Type: text/html; charset=ISO-8859-1",true);
			$sql = "SELECT id
					FROM categorias
					WHERE subordinadoid='$subordinadoid'";
			$mysql = mysql_query($sql);
			if(mysql_num_rows($mysql) > 0){
				$xml = "$xml";
			}
			break;
		}
        default :
            $xml = "default";

    }

    
//    if(!empty($xml)){
        echo $xml;
//    }
?>                                                                                                           
