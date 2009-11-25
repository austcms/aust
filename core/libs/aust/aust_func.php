<?php
//	$aust = Array();
	// funÃ§Ã£o que pega o patriarca de determinada categoria
	$austsubnome = "";
	$austpatriarca = "";
	function getAustFather($table, &$austsubnome, &$austpatriarca, $w){
		$sql = "SELECT
					id, nome, patriarca
				FROM
					$table
				WHERE
					id = '".$w."'";
		
		$mysql = mysql_query($sql);
		$dados = mysql_fetch_array($mysql);
		$austsubnome = $dados[nome];
		$austpatriarca = !empty($dados[patriarca]) ? $dados[patriarca] : $austsubnome;
	}

	// funÃ§Ã£o que monta as categorias em arrays e depois cria argumentos SQL
	function BuildDDListItems($table, $area, &$aust2, $parent=0, $level=0, $current_node=-1){

    	$where = "lp.subordinadoid = '$parent'";
		if($parent == 0){
			$where = $where . " AND lp.nome='".$area."'";
		}
		$sql="SELECT
					lp.id, lp.subordinadoid, lp.nome, lp.classe,
					( SELECT COUNT(*)
						FROM
							$table As clp
						WHERE
							clp.subordinadoid=lp.id
					) As num_sub_nodes
				FROM
					$table AS lp
				WHERE
					$where
		";
		$mysql = mysql_query($sql);
		echo $iSQL;
		$i = 0;

		while ($myrow = mysql_fetch_array($mysql)){
			//se fÃ´r um nÃ³, serÃ£o efectuadas algumas modificaÃ§Ãµes


			//construir item
//			if($myrow[classe] == "estrutura")
//				$tipo = $myrow[nome];

			$aust2[$myrow[id]] = $myrow[nome];
			$austsub[$myrow[id]] = $myrow[subordinadoid];
			$austtipo[$tipo] = $myrow[id];
			echo $myrow[id] .' - '. $myrow[nome] .' - '. $austsub[$myrow[id]] .' - '.$tipo.'<br />';
			
			//chamar recursivamente a funÃ§Ã£o
			$items.=BuildDDListItems($table, $area, $aust2, $myrow["id"], $level+1, $current_node);

		}
    }

//	function _IsNode($num_nodes,$parent){ return ($num_nodes>0 || $parent==0); }
/*
	$wherecat = '';
	for($i = 0; $i < count($aust2); $i++){
		if($i < count($aust2)-1)
			$wherecat = $wherecat . ' content.categoria='. key($aust2) . ' OR';
		else
			$wherecat = $wherecat . ' content.categoria='. key($aust2) . '';
		if($aust2[key($aust2)] == "Menu") $menuid = key($aust2);
		if($aust2[key($aust2)] == "Agenda de Eventos") $agendaid = key($aust2);
		$globalaust[key($aust2)] = $aust2[key($aust2)];
		next($aust2);
	}

	
	// Escreve na tela as subcategorias
	function BuildCategoriasStructure($table, $parent=0, $level=0, $url){
		$sql = "
				SELECT
					cat.id, cat.subordinadoid, cat.nome, cat.autorid,
					( SELECT COUNT(*)
					FROM
						$table AS clp
					WHERE
						clp.subordinadoid=cat.id
					) AS num_sub_nodes
				FROM
					$table AS cat
				WHERE
					cat.subordinadoid = '$parent'
			";
//										echo $sql;
		$mysql = mysql_query($sql);
		while($dados = mysql_fetch_array($mysql)){
		
			?>
			<div class="structure" id="structure<?=$level;?>" style="margin-left: <? echo $level*40;?>px">
				<?php if($dados[num_sub_nodes] > 0) echo '+ '; else echo '- '; ?><a href="<?php echo $url . $dados[id]; ?>"><?=$dados[nome];?></a>
			</div>
			<?
			if($dados[num_sub_nodes] > 0){
				BuildCategoriasStructure($table, $dados[id], $level+1, $url);
			}
		}
	}

*/
?>
