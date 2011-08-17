<?php

/*
 * FORMULÁRIO
 */
/*
 * Carrega configurações automáticas do DB
 */

	$params = array(
		"aust_node" => $_GET["aust_node"],
	);

	$moduloConfig = $module->loadModConf($params);

	// tem editor?
	if( $module->getStructureConfig('description_has_rich_editor') == '1' )
		$module->loadHtmlEditor();
	

/*
 * Ajusta variáveis iniciais
 */
	$w = (!empty($_GET['w'])) ? $_GET['w'] : '';
	$austNode = $_GET['aust_node'];

/*
 * [Se novo conteúdo]
 */
	if($_GET['action'] == 'create'){
		$tagh1 = "Criar: ". Aust::getInstance()->getStructureNameById($_GET['aust_node']);
		$tagp = 'Crie um novo conteúdo abaixo.';
		$dados = array('id' => '');
	}
/*
 * [Se modo edição]
 */
	else if($_GET['action'] == 'edit'){
		$tagh1 = "Editar: ". Aust::getInstance()->getStructureNameById($_GET['aust_node']);
		$tagp = 'Edite o conteúdo abaixo.';
		$sql = "
				SELECT
					id,
					node_id,
					title,
					title_encoded,
					subtitle,
					summary,
					description,
					link,
					order_nr,
					file_bytes,
					file_name,
					file_type,
					reference,
					reference_id,
					local,
					class,
					specie,
					expire_on,
					created_on,
					updated_on,
					pageviews,
					admin_id
				FROM
					".$module->getMainTable()."
				WHERE
					id='$w'
				";
		$query = $module->connection->query($sql, "ASSOC");
		$dados = $query[0];
		$frmcategory = $dados['node_id'];
	}

	$frmcategory = ( empty($frmcategory) )
				? $austNode
				: $frmcategory;

?>
<h2><?php echo $tagh1;?></h2>
<p><?php echo $tagp;?></p>



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data">
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">
<?php if($_GET['action'] == 'create'){ ?>
	<input type="hidden" name="frmcreated_on" value="<?php echo date("Y-m-d"); ?>">
	<input type="hidden" name="frmaadmin_id" value="<?php echo $_SESSION['loginid'];?>">
<?php } else { ?>

	<input type="hidden" name="frmupdated_on" value="<?php date("Y-m-d H:i:s"); ?>">
	<input type="hidden" name="frmadmin_id" value="<?php ifisset( $dados['admin_id'] );?>">

<?php }?>
<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">
<input type="hidden" name="frmnode_id" value="<?php echo $frmcategory; ?>">
<table class="form">


	<?php
	/*
	 * CATEGORY_SELECTION
	 *
	 */
	if( $module->getStructureConfig('category_selection') ){
		?>
		<tr>
			<td class="first"><label>Categoria:</label></td>
			<td class="second">
				<div id="categoriacontainer">

				<?php
				echo BuildDDList( Registry::read('austTable') ,'frmnode_id', User::getInstance()->tipo , $austNode, $frmcategory);
				?>


				</div>
				<?php
				if( $module->getStructureConfig('category_creation') ){
					lbCategoria($austNode);
				}
				?>
			</td>
		</tr>
		<?php
	}
	
	
	/*
	 * EXPIRETIME PANEL
	 *
	 * Se expireTime está ativo, mostra que o tempo está expirado
	 */
	if( $module->getStructureConfig("expireTime") ){

		if( !empty($dados["expire_on"]) ){
			$date = date('Y-m-d', strtotime($dados["expire_on"]));
		} else {
			$date = "";
		}

		if( !empty($date) ){
			$today = date("Y-m-d");
			$tDate = strtotime($date);
			$tToday = strtotime($today);

			$expired = false;
			if( ($tToday-$tDate) > 0 ){
				$expired = true;
			}
		}
		if( $expired ){
			?>
			<tr>
				<td colspan="2">
					<div class="insucesso">
					Este conteúdo já expirou e não está mais sendo mostrado.
					</div>
					<br />
				</td>
			</tr>
			<?php
		}
	}
	?>

	<?php
	/*
	 * Mostra imagem preview
	 */
	if( !empty($dados["file_bytes"]) && $dados["file_bytes"] > 0 ){
		?>
		<tr>
			<td valign="top"><label>Imagem atual:</label></td>
			<td>
				<img src="core/libs/imageviewer/visualiza_foto.php?table=images&thumbs=yes&myid=<?php echo $dados["id"]; ?>&maxxsize=450&maxysize=400" />
				<p class="explanation">
				Imagem cadastrada atualmente. Para alterá-la, envie uma nova no formulário abaixo.
				</p>
			</td>
		</tr>
		<?php
	}
	?>

	<tr>
		<td valign="top"><label>Arquivo:</label></td>
		<td>
			<input type="file" name="frmarquivo" onchange="validateFile();" />
			<script type="text/javascript">
				var fileMimeType = '<?php echo $dados["file_type"] ?>';
			</script>
			<p class="explanation">
			Selecione a imagem que será carregada.
			</p>
		</td>
	</tr>
	<tr>
		<td valign="top"><label>Título:</label></td>
		<td>
			<INPUT TYPE='text' NAME='frmtitle' class='text' value='<?php if( !empty($dados['title']) ) echo $dados['title'];?>' />
			<p class="explanation">
			Um título. Sua utilidade básica é você identificar este item na listagem.
			</p>
		</td>
	</tr>

		<?php
	/*
	 * DESCRIÇÃO
	 */
	if( $module->getStructureConfig("link") ){
		?>
		<tr>
			<td valign="top"><label>Link:</label></td>
			<td>
				<INPUT TYPE='text' id='link' NAME='frmlink' class='text' value='<?php if( !empty($dados['link']) ) echo $dados['link'];?>' />
				<p class="explanation link_explanation" id="explanation_link_file_is_image">
				Se você deseja que, ao clicar na imagem, o usuário seja levado a
				um endereço específico, digite-o acima (ex.: http://www.meusite.com.br/noticia/27). Não
				se esqueça de inserir http:// no início.
				</p>
				<p class="explanation link_explanation" id="explanation_link_file_is_flash">
				Se você quer inserir links em animações Flash, você precisa fazer isto dentro
				do próprio arquivo Flash (usando ActionScript).
				</p>

				<?php /* this span serves javascript purposes */ ?>
				<span id="post_link"></span>
				<script type="text/javascript"> validateFile(); </script>

			</td>
		</tr>
		<?php
	}
	?>

	<?php
	/*
	 * RESUMO
	 */
	if( $module->getStructureConfig("summary") ){
	?>
	<tr>
		<td valign="top"><label>Resumo:</label></td>
		<td>
			<INPUT TYPE='text' NAME='frmsummary' class='text' value='<?php if( !empty($dados['summary']) ) echo $dados['summary'];?>' />
			<p class="explanation">

			</p>
		</td>
	</tr>
	<?php
	}
	?>

	<?php
	/*
	 * ORDEM
	 */
	if( $module->getStructureConfig("ordem") || $module->getStructureConfig("ordenate") ){
	?>
	<tr>
		<td valign="top"><label>Ordem:</label></td>
		<td>
			<select name="frmordem" class="select">
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '10'); ?> value="10">10</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '9'); ?> value="9">9</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '8'); ?> value="8">8</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '7'); ?> value="7">7</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '6'); ?> value="6">6</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '5'); ?> value="5">5</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '4'); ?> value="4">4</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '3'); ?> value="3">3</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '2'); ?> value="2">2</option>
				<option <?php if( !empty($dados['order_nr']) ) makeselected($dados['order_nr'], '1'); ?> value="1">1</option>
			</select>
			<p class="explanation">
				Selecione um número que representa a importância deste item.
				Quanto maior o número, maior a prioridade.
			</p>
		</td>
	</tr>
	<?php
	}
	?>

	<?php
	/*
	 * DESCRIÇÃO
	 */
	if( $module->getStructureConfig("description") ){
		?>
		<tr>
			<td valign="top"><label>Descrição: </label>
			</td>
			<td>
				<textarea name="frmdescription" id="jseditor" rows="8" style="width: 400px"><?php if( !empty($dados['description']) ) echo $dados['description'];?></textarea>
			<br />
			</td>
		</tr>
		<?php
	}
	?>

	<?php
	/*
	 * EXPIRETIME
	 *
	 * Quando este conteúdo deve parar de aparecer
	 */
	$showExpireTime = false;
	if( !empty($moduloConfig["expireTime"]) && !empty($moduleConfig["expireTime"]["valor"]) ){
		if( $moduloConfig["expireTime"]["valor"] == "1" )
			$showExpireTime = true;
	}
	if( $showExpireTime ){

		if( !empty($dados["expire_on"]) ){
			$day = date('d', strtotime($dados["expire_on"]));
			$month = date('m', strtotime($dados["expire_on"]));
			$year = date('Y', strtotime($dados["expire_on"]));
		} else {
			$day = "";
			$month = "";
			$year = "";
		}
	?>
	<tr>
		<td valign="top"><label>Expirar em:</label></td>
		<td>
			<input type='text' name='grouped_data[expire_on][day]' value='<?php echo $day; ?>' size="1" maxlength="2" />
			<input type='text' name='grouped_data[expire_on][month]' value='<?php echo $month; ?>' size="1" maxlength="2" />
			<input type='text' name='grouped_data[expire_on][year]' value='<?php echo $year; ?>' size="3" maxlength="4" />
			<input type='hidden' name='grouped_data[expire_on][_options][divisor]' value='-' />
			<input type='hidden' name='grouped_data[expire_on][_options][type]' value='date' />
			<p class="explanation">
			<strong>Formato dd/mm/aaaa (dia/mes/ano)</strong>
			<br />Indique qual o último dia que este conteúdo deve ser mostrado.
			<br />Deixe em branco para não expirar.
			</p>
		</td>
	</tr>
	<?php
	}
	?>

	<tr>
		<td colspan="2" style="padding-top: 10px;"><center><INPUT TYPE="submit" value="Enviar" name="submit" class="submit"></center></td>
	</tr>
</table>

</form>
