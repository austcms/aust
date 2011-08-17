<?php
$referencia = $value["tipo"]["tabelaReferencia"];
$tabelaRelacional = $value["tipo"]["referencia"];
$campo = $value["tipo"]["tabelaReferenciaCampo"];

if( !empty($value["tipo"]["refParentField"]) )
	$parentField = $value["tipo"]["refParentField"];
else
	$parentField = $referencia.'_id';

if( !empty($value["tipo"]["refChildField"]) )
	$childField = $value["tipo"]["refChildField"];
else
	$childField = $referencia.'_id';
	
$sql = "SELECT
			t.id, t.$campo
		FROM
			".$referencia." AS t
		ORDER BY t.$campo ASC
		";

$checkboxes = $module->connection->query($sql);

$inputName = "data[".$infoCadastro["structure"]["table"]["value"]."][".$chave."][]";

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
	$values = $module->connection->query($sql);

	if( empty($values)){
		$values = array();
	} else {
		foreach( $values as $id ){
			$value["valor"][] = $id["referencia"];
		}
	}
}

$dragdrop = '';
if( $module->getFieldConfig($chave, '1n_has_dragdrop') == '1' )
	$dragdrop = 'dragdrop';

/*
 * PESQUISAR
 */
?>
<div class="input">
	<label for="input-<?php echo $value["chave"] ?>"><?php echo $value["label"]; ?></label>
	
	<div class="input_field input_checkbox input_relacionamentos">
		<div id="search1n_<?php echo $chave; ?>" class="search_1n">
			<label>Pesquisar: <input type="text" name="search" onkeyup="javascript: search1n(this);"
				autocomplete='off'
				onkeydown="javascript: if( event.keyCode == 13) return false;"
				data-austnode="<?php echo $austNode; ?>"
				data-field="<?php echo $chave; ?>"
				data-relational_table="<?php echo $tabelaRelacional; ?>"
				data-w="<?php echo $w; ?>"
				data_table="<?php echo $infoCadastro["structure"]["table"]["value"]?>"
				data-ref_field="<?php echo $campo; ?>"
				data-ref_table="<?php echo $referencia; ?>"
				data-input_name="<?php echo $inputName ?>"
				data-child_field="<?php echo $childField ?>"
				data-parent_field="<?php echo $parentField ?>"
			/></label>
		</div>
		
		
		<div id="search1n_<?php echo $chave; ?>_result" class="search1n_result <?php echo $dragdrop ?>">
			<?php
			foreach( $values as $value ){
				?>
				<div>
				<div class="input_checkbox_each input_checkbox_<?php echo $chave ?>">
					<input type="checkbox" class="original checkbox_<?php echo chave;?>" value="<?php echo $value['ref_id'] ?>" checked="checked"
						name="<?php echo $inputName ?>"> <?php echo $value['ref_value'] ?>
				</div>
				</div>
				<?php
			}
			?>
			<script type="text/javascript">
			// marca o checkbox que estiver desmarcado após refresh do navegador
			$('.input_checkbox_<?php echo $chave; ?> input.original').attr('checked', false);
			$('.input_checkbox_<?php echo $chave; ?> input.original').attr('checked', true);
			</script>
		</div>
	</div>
</div>	

<?php

/**
 * Cria INPUT
 */
/*
echo $form->input( $chave, array(
								"label" => $value["label"],
								"select" => $select,
								"checkbox" => $checkbox,
								"value" => $value["valor"],
								"type" => $inputType,
								'after' => '<p class="explanation">'.$value['comentario'].'</p>'
							)
					);
*/
?>
