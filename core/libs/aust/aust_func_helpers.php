<?php
/**
* Construir Multiple-Level Optgroup
*
* @param String ID
* @return String
*/
function BuildDDList($table,$id="ddListOptions",$escala,$parent=0, $current_node=-1,$primeirovazio=false, $optionsOnly=false){
	$ddListItems=_BuildDDListItems($table,$escala,$parent, 0, $current_node);

	if($optionsOnly == true){
		return "$ddListItems";
	} else if($primeirovazio == true){
		return "
		<select name='$id' id='$id' class=\"aust_node\">
		<option value=''>Nenhum</option>
		$ddListItems</select>
		";
	} else {
		return "
		<select name='$id' id='$id' class=\"aust_node\">$ddListItems</select>
		";
	}
}

/**
* Construir item da lista
*
* @param Int Pertence a
* @param Int Nivel de Profundidade
* @param DBConnection Ligação à BD
* @return String
*/
function _BuildDDListItems($table, $escala = '', $parent=0, $level=0, $current_node=-1){

	global $conexao;
	$indent = "";

	for ($i=0; $i<$level; $i++){
		$indent.= _DummySpaces(7); //fazer indentação com 4 espaços
	}

	$iSQL="
		SELECT
		lp.id, lp.father_id, lp.name, lp.type,
		( SELECT COUNT(*)
			FROM
			$table AS clp
			WHERE
			clp.father_id=lp.id
		) As num_sub_nodes
		FROM
			$table AS lp
		WHERE
			lp.father_id = '$parent'
	";
	//	  echo $iSQL;
	$query = Connection::getInstance()->query($iSQL);
	$items = '';

	foreach ($query as $myrow){
  		//se fôr um nó, serão efectuadas algumas modificações
		  if (_IsNode($myrow["num_sub_nodes"],$myrow["father_id"])){
			  $prefix="";
			  $class='class="node"';
		  }else{
			  $prefix="&bull;"._DummySpaces(1);
			  $class = '';
		  }

		  $tmp_nome = $myrow['name'];
		  $selected = '';
		  if($current_node == $myrow['id']){
			  $tmp_nome.= ' <- Atualmente';
			  $selected = 'selected="selected" style="font-weight: bold;"';
		  }
		  //construir item
		  if($myrow["father_id"] == 0){
			  if($escala == "webmaster"){
				  $items.="<option value='".$myrow['id']."' $selected $class>$indent$prefix$tmp_nome</option>";
			  } else {
				  $items.='<optgroup label="'.$tmp_nome.'"></optgroup>';
			  }
		  } else {
			  $items.="<option value='".$myrow['id']."' $selected $class>$indent$prefix$tmp_nome</option>";
		  }

		  //chamar recursivamente a função
		  $items.=_BuildDDListItems($table, $escala, $myrow['id'], $level+1, $current_node);
	}
	return $items;
}

/**
* Verificar se o registo é 1 nó
*
* @param Int
* @param Int
* @return Boolean
*/
function _IsNode($num_nodes,$parent){ return ($num_nodes>0 || $parent==0);}

/**
* Construir espaços (&nbsp;)
*
* @param Int Número
* @return String
*/
function _DummySpaces($num){
	$dummy = '';
	for ( $i=0;$i<$num;$i++ )
		$dummy.="&nbsp;";
	return $dummy;
}

?>