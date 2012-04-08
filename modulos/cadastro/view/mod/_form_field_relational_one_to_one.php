<?php
$referencia = $valor["tipo"]["tabelaReferencia"];
$tabelaRelacional = $valor["tipo"]["referencia"];
$campo = $valor["tipo"]["tabelaReferenciaCampo"];

if( !empty($valor["tipo"]["refParentField"]) )
	$parentField = $valor["tipo"]["refParentField"];
else
	$parentField = $referencia.'_id';

if( !empty($valor["tipo"]["refChildField"]) )
	$childField = $valor["tipo"]["refChildField"];
else
	$childField = $referencia.'_id';
	
$sql = "SELECT
            t.id, t.$campo
        FROM
            ".$referencia." AS t
        ORDER BY t.$campo ASC
        ";

$checkboxes = $modulo->connection->query($sql);

$inputName = "data[".$infoCadastro["estrutura"]["tabela"]["valor"]."][".$chave."]";

foreach($checkboxes as $tabelaReferenciaResult){
    $checkbox["options"][ $tabelaReferenciaResult["id"] ] = $tabelaReferenciaResult[ $campo ];
}

/*
 * Se for edição, pega os dados que estão salvos neste campo
 */
$values = array();
if( !empty($w) ){
    $sql = "SELECT
                t.id, t.".$childField." AS ref_id,
				r.".$campo." as ref_value
            FROM
                ".$tabelaRelacional." AS t
			LEFT JOIN
				".$referencia." AS r
			ON
				r.id=t.".$childField."
			WHERE
				t.".$parentField."='".$w."'
            ORDER BY
                t.order_nr ASC, t.id ASC
            ";
    $values = $modulo->connection->query($sql);

    if( empty($values)){
        $values = array();
    } else {
        foreach( $values as $id ){
            $valor["valor"][] = $id["referencia"];
        }
    }
}

$dragdrop = '';
if( $modulo->getFieldConfig($chave, '1n_has_dragdrop') == '1' )
	$dragdrop = 'dragdrop';

/*
 * PESQUISAR
 */
?>
<div class="input">
	<label for="input-<?php echo $parentField ?>"><?php echo $valor["label"]; ?></label>
	
	<div class="input_field input_checkbox input_relacionamentos">
		<div id="search1_1_<?php echo $chave; ?>" class="search_1n">
			<label>Pesquisar: <input type="text" name="search" onkeyup="javascript: search1_1(this);"
				autocomplete='off'
				onkeydown="javascript: if( event.keyCode == 13) return false;"
				data-austnode="<?php echo $austNode; ?>"
				data-field="<?php echo $chave; ?>"
				data-relational_table="<?php echo $tabelaRelacional; ?>"
				data-w="<?php echo $w; ?>"
				data_table="<?php echo $infoCadastro["estrutura"]["tabela"]["valor"]?>"
				data-ref_field="<?php echo $campo; ?>"
				data-ref_table="<?php echo $referencia; ?>"
				data-input_name="<?php echo $inputName ?>"
				data-child_field="<?php echo $childField ?>"
				data-parent_field="<?php echo $parentField ?>"
			/></label>
		</div>
		
    <?php
    /* current value */

		if( $_GET['action'] == EDIT_ACTION ){
      $sql = "SELECT id, ".$valor["tipo"]["tabelaReferenciaCampo"]." 
              FROM ".$valor["tipo"]["tabelaReferencia"] ."
              WHERE id = '".$valor['valor']."'";

      $selectedValue = $conexao->query($sql);
      $selectedValue = reset($selectedValue);
      $selectedValue = $selectedValue[$valor["tipo"]["tabelaReferenciaCampo"]];
		}
    ?>
		
		<div id="search1_1_<?php echo $chave; ?>_result" class="search1_1_result <?php echo $dragdrop ?>">
			<?php
      if( !empty($selectedValue)) 
        echo "Atual: " . $selectedValue;
			?>
		</div>
	</div>
</div>	

<input type="hidden" name="<?php echo $inputName; ?>" id="search1_1_<?php echo $chave; ?>_id" value="<?php echo $valor["valor"]; ?>"/>
