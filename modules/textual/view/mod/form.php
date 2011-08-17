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

	$editorPlugins = '';
	if( $module->getStructureConfig('upload_inline_images') == '1' )
		$editorPlugins = 'imagemanager';
	
	$module->loadHtmlEditor($editorPlugins);


/*
 * Ajusta variáveis iniciais
 */
	$austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';

/*
 * [Se novo conteúdo]
 */
	if($_GET['action'] == 'create'){
		$tagh2 = "Criar: ". Aust::getInstance()->getStructureNameById($_GET['aust_node']);
		$tagp = 'Crie um novo conteúdo abaixo.';
		$dados = array('id' => '');
	}
/*
 * [Se modo edição]
 */
	else if($_GET['action'] == 'edit'){

	}

/*
 * Tamanho máximo do Upload.
 */
$maxSize = (int) str_replace('M','', ini_get('upload_max_filesize') );
if( (int) str_replace('M','', ini_get('post_max_size') ) < $maxSize )
	$maxSize = (int) str_replace('M','', ini_get('post_max_size') );


?>
<h2><?php echo $tagh2;?></h2>
<p><?php echo $tagp;?></p>



<form method="post" action="adm_main.php?section=<?php echo MODULES ?>&action=save" enctype="multipart/form-data" >
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">

<?php if($_GET['action'] == 'create'){ ?>
	<input type="hidden" name="frmadmin_id" value="<?php echo User::getInstance()->getId(); ?>">
<?php }?>

<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">

<table cellpadding=0 cellspacing=0 class="form">
	
	<?php if( $module->isEdit() && $module->getStructureConfig("show_visits_counter") ){ ?>
	<tr>
		<td><label>Estatísticas:</label></td>
		<td>
			<?php
			if( $dados['pageviews'] == 0 ){
				?>
				Nenhum visitante viu este conteúdo até agora.
				<?php
			} else if( $dados['pageviews'] == 1 ){
				?>
			 	Apenas <strong>um</strong> visitante viu este conteúdo.
				<?php
			} else if( $dados['pageviews'] > 1 ){
				?>
			 	Este conteúdo foi visto por <strong><?php echo $dados['pageviews']?></strong> 
				visitantes.
				<?php
			}
			?>
		</td>
	</tr>
	<?php } ?>

	<?php
	$slave = Aust::getInstance()->getRelatedSlaves($_GET['aust_node']);
	if( !empty($slave) && $module->isEdit() ){
		$slave = reset($slave);
		?>	
		<tr>
			<td valign="top"><label>Opções:</label></td>
			<td valign="top">
				<?php
				foreach ($slave as $key => $value) {
					?>
					<div>
					<a href="adm_main.php?section=content&action=edit&aust_node=<?php echo $value['slave_id']?>&related_master=<?php echo $_GET['aust_node']?>&related_w=<?php echo $_GET['w']?>">
					<?php echo $value['slave_name']; ?>
					</a>
					</div>
					<?php
				}
				$slave = reset($slave);
				?>
			</td>
		</tr>
		<?php
	}
	?>
	
	<?php if( $module->getStructureConfig("aust_node_selection") ){ ?>
		<tr>
			<td class="first"><label>Categoria:</label></td>
			<td class="second">
				<div id="categoriacontainer">
				<?php
				$current_node = '';
				if( $_GET['action'] == "editar" || $_GET['action'] == "edit" ){
					$current_node = $dados['node_id'];
					?>
					<input type="hidden" name="frmnode_id" value="<?php echo $current_node; ?>">
					<?php
				}

				echo BuildDDList( Registry::read('austTable') ,'frmnode_id', User::getInstance()->tipo ,$austNode, $current_node);
				?>


				</div>
				<?php
				if( $this->module->getStructureConfig("new_aust_node") == "1" || User::getInstance()->isRoot() ){
					lbCategoria($austNode);
				}
				?>
			</td>
		</tr>
	<?php } ?>
	<tr>
		<td><label>Título:</label></td>
		<td>
			<INPUT TYPE='text' NAME='frmtitle' class='text' value='<?php if( !empty($dados['title']) ) echo $dados['title'];?>' />
			<p class="explanation">

			</p>
		</td>
	</tr>
	
	<?php
	/*
	 * PREVIEW URL
	 */
	if( $module->getStructureConfig("has_file") ){ ?>
	<tr>
		<td valign="top"><label>Arquivo:</label></td>
		<td>
			<?php
			if( $_GET['action'] == "edit" && !empty($dados['file_systempath']) ){
				?>
				<img src="<?php echo getFileIcon($dados['file_name']);?>" align="left" />
				<span style="position: relative; left: 7px; top: 12px; display: block">
					<strong>
					<?php
					if( empty($dados['original_file_name']) )
						echo lineWrap($dados['file_name'], 64);
					else
						echo lineWrap($dados['original_file_name'], 64);
					?>
					</strong>
					<br />
					<span class="filesize">
						<?php echo convertFilesize( $dados['file_size'] ); ?>Mb
					</span>
				</span>

				<br clear="all"/>

				<input type="file" name="file" />
				<p class="explanation">
					Selecione um arquivo para substituir o atual.
					Tamanho máximo: <?php echo $maxSize; ?>Mb.
					
				</p>
				<?php
			} else {
				?>
				<input type="file" name="file" />
				<p class="explanation">
					Localize um arquivo se você deseja realizar upload.
					Tamanho máximo: <?php echo $maxSize; ?>Mb.
				</p>
			<?php } ?>
	
		</td>
	</tr>
	<?php } ?>

	<?php
	/*
	 * PREVIEW URL
	 */
	if( $module->isEdit() AND $module->getStructureConfig("generate_preview_url") ){ ?>
	<tr>
		<td valign="top"><label>URL gerada:</label></td>
		<td>
			<?php echo $module->getGeneratedUrl(); ?>
			<?php
			tt('Esta URL é gerada automaticamente e aponta para página deste conteúdo.<br /><br />'.
			   'Em caso de alterações '.
			   'no site principal, será necessário atualizar este valor');
			?>
		</td>
	</tr>
	<?php } ?>
	<?php
	/*
	 * RESUMO
	 */ 

	if( $module->getStructureConfig("summary") ){
	?>
	<tr>
		<td valign="top"><label>Resumo:</label></td>
		<td>
			<textarea name="frmsummary" rows="2"><?php if( !empty($dados['summary']) ) echo $dados['summary'];?></textarea>
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
	$showOrdem = false; // por padrão, não mostra
	if( $module->getStructureConfig("ordenate") ) {
		?>
		<tr>
			<td valign="top"><label>Ordem:</label></td>
			<td>
				<select name="frmorder_nr" class="select">
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
	if( 1 == 0 AND $module->getStructureConfig("manual_date") ){
	?>
	<tr>
		<td valign="top"><label>Data manual:</label></td>
		<td>
			<input type="text" name="frmcreated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
			<p class="explanation">
				Configure uma data para o conteúdo manualmente.
			</p>
		</td>
	</tr>
	<?php
	}
	?>

	<tr>
		<td colspan="2"><label>Texto: </label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="frmtext" id="jseditor"><?php if( !empty($dados['text']) ) echo $dados['text'];?></textarea>
		<br />
		</td>
	</tr>

	<tr>
		<td colspan="2"><center><INPUT TYPE="submit" value="Enviar" name="submit" class="submit"></center></td>
	</tr>
</table>

</form>
