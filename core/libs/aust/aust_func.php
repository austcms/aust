<?php
	$austsubnome = "";
	$austpatriarca = "";

	// funÃ§Ã£o que monta as categorias em arrays e depois cria argumentos SQL
	function BuildDDListItems($table, $area, &$aust2, $parent=0, $level=0, $current_node=-1){

		$where = "lp.father_id = '$parent'";
		if($parent == 0){
			$where = $where . " AND lp.name='".$area."'";
		}
		$sql="SELECT
					lp.id, lp.father_id, lp.name, lp.class,
					( SELECT COUNT(*)
						FROM
							$table As clp
						WHERE
							clp.father_id=lp.id
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

			$aust2[$myrow[id]] = $myrow[name];
			$austsub[$myrow[id]] = $myrow[father_id];
			$austtipo[$tipo] = $myrow[id];
			echo $myrow[id] .' - '. $myrow[name] .' - '. $austsub[$myrow[id]] .' - '.$tipo.'<br />';
			
			//chamar recursivamente a funÃ§Ã£o
			$items.=BuildDDListItems($table, $area, $aust2, $myrow["id"], $level+1, $current_node);

		}
	}

?>