<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="expires" content="Mon, 19 Feb 2024 11:12:01 GMT" />
	<title><?php echo Aust::getInstance()->getStructureNameById($_GET['aust_node'])?> - Versão para impressão</title>
	<link rel="stylesheet" type="text/css" href="<?php echo UI_PATH; ?>css/special/print.css" media="screen, print" />
</head>
<body>
<?php
/**
 * Formulário deste módulo
 *
 * @since v0.1.6 09/07/2009
 */
/**
 * Informações deste cadastro
 */
$infoCadastro = $module->pegaInformacoesCadastro($austNode);

if( !empty($_GET["w"]) ){
	$w = $_GET['w'];
}

//pr($infoCadastro);
?>
<h2>Cadastro: <?php echo Aust::getInstance()->getStructureNameById($_GET['aust_node'])?></h2>

<div id="print">
<table>
	<tbody>
	<?php

	/**
	 * MOSTRA FORMULÁRIO DINÂMICO
	 */

		/**
		 * Se edição
		 */
		if( !empty($_GET['w']) ){
			/**
			 * Cria INPUT Hidden com ID
			 */
		}

	/**
	 * Campos
	 */
	//pr($infoCadastro);
	$tabelaCadastro = $infoCadastro["structure"]['table']["valor"];

	/*
	 *
	 * FORMULÁRIO DE CADASTRO
	 *
	 * O formulário é criado automaticamente
	 *
	 */
	foreach( $camposForm as $chave=>$value ){

		unset($inputType);
		$select = array();
		$checkbox = array();

		if( array_key_exists($value['nomeFisico'], $divisorTitles) ){
			?>
			<tr valign="top">
				<td class="divisor" colspan="2">
				<h3><?php echo $divisorTitles[$value['nomeFisico']]['valor']; ?></h3>
				<?php
				if( !empty($divisorTitles[$value['nomeFisico']]['comentario']) ){
					echo '<p>'.$divisorTitles[$value['nomeFisico']]['comentario'].'</p>';
				}
				?>
				</td>
			</tr>
			<?php
		}

		/**
		 * RELACIONAL UM PARA UM
		 */
		if( $value["tipo"]["especie"] == "relacional_umparaum" ){
			$sql = "SELECT id,".$value["tipo"]["tabelaReferenciaCampo"]." FROM ".$value["tipo"]["tabelaReferencia"];
			$selectValues = Connection::getInstance()->query($sql);
			//pr($sql);
			//$select["selected"] = "3";
			$inputType = "select";
			foreach($selectValues as $tabelaReferenciaResult){
				$select["options"][ $tabelaReferenciaResult["id"] ] = $tabelaReferenciaResult[ $value["tipo"]["tabelaReferenciaCampo"] ];
			}

		}
		/*
		 * RELACIONAL UM PARA MUITOS
		 *
		 * Monta checkboxes do campo que é do tipo relacional um-para-muitos
		 */
		else if($value["tipo"]["especie"] == "relacional_umparamuitos") {

			$referencia = $value["tipo"]["tabelaReferencia"];
			$tabelaRelacional = $value["tipo"]["referencia"];
			$campo = $value["tipo"]["tabelaReferenciaCampo"];
			$sql = "SELECT
						t.id, t.$campo
					FROM
						".$referencia." AS t
					ORDER BY t.$campo ASC
					";
			$checkboxes = $module->connection->query($sql);

			$inputType = "checkbox";
			foreach($checkboxes as $tabelaReferenciaResult){
				$checkbox["options"][ $tabelaReferenciaResult["id"] ] = $tabelaReferenciaResult[ $campo ];
			}

			/*
			 * Se for edição, pega os dados que estão salvos neste campo
			 */

			if( !empty($w) ){
				$sql = "SELECT
							t.id, t.".$referencia."_id AS referencia
						FROM
							".$tabelaRelacional." AS t
						ORDER BY
							t.id ASC
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
		} elseif( $value['tipo']['tipoFisico'] == 'date' ){
			$inputType = "date";
		} elseif( $value['tipo']['tipoFisico'] == 'text' ){
			$inputType = "textarea";
		}

		if( empty($value["valor"]) ){
			$value["valor"] = "";
		}


		if( empty($inputType) ){
			$inputType = "";
		}

		//pr($inputType);

		/**
		 * Cria INPUT
		 */
		?>
		<tr valign="top">
			<td class="label">
			<?php
			echo $value["label"];
			?>:
			</td>
			<td valign="top">
			<?php
			if( empty($value["valor"]) )
				echo '-';
			else
				echo $value["valor"];
			?>
			</td>
		</tr>
		<?php
		/*
		echo $form->input( $chave, array(
										"label" => $value["label"],
										"select" => $select,
										"checkbox" => $checkbox,
										"value" => $value["valor"],
										"type" => $inputType,
									)
							);
		 *
		 */
	}



	?>
	</tbody>
</table>
</div>

</body>
</html>