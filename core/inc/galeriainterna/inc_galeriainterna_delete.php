<?
if (!empty($action)){
	if ($action == "delete"){
		if ($confirmed == "yes"){
			$sql4 = "SELECT * FROM Noticias WHERE id='$idtodelete' AND autor='$login1'";
            //echo $sql4;
	
		    $mysql4 = Connection::getInstance()->query($sql4);
		    $total = count($mysql4);
		    $dados4 = $mysql4[0];
			if ($total == 0){
				echo "<font color=\"red\">Esta galeria não existe.</font><br><br>";
				//break;
			} else { 					  
				$sql1 = "DELETE FROM Noticias WHERE id='$idtodelete' AND autor='$login1'";
			    if (Connection::getInstance()->exec($sql1))
			        echo "Ítem deletado com sucesso.<br><br>";
			    else
			        echo "Erro ao deletar ítem.<br><br>";
			}
		} else {
		    echo "<font color=\"red\">";
		    echo "Tem certeza que deseja apagar a galeria inteira?<br>";
		    echo "<a href=\"adm_main.php?section=galeria&action=delete&idtodelete=". $idtodelete ."&confirmed=yes\" class=\"link_adm\" target=\"_top\">Deletar galeria inteira</a> - ";
		    echo "<a href=\"adm_main.php?section=galeria_list\" class=\"link_adm\" target=\"_top\">Cancelar ação</a><br><br>";
		    echo "</font>";
		}
	}
}
?>