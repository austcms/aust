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
?>
<h2>Filtro <?php echo $tagh2;?></h2>
<p>Os itens abaixo referem-se ao filtro <?php echo $tagh2;?>.</p>

<?php // form permits selecting rows and acting on them ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">

	<?php
//	$redirectTo = "adm_main.php?section=".$_GET['section'].'&action=view&aust_node='.$_GET['aust_node'].'&w='.$_GET['w'];
	$redirectTo = $_SERVER['REQUEST_URI'];
	?>
	<input type="hidden" name="redirect_to" value="<?php echo $redirectTo; ?>" />
	<input type="hidden" name="w" value="<?php echo $w; ?>" />

	<?php
	if( !empty($query['results']) &&
 		$this->showControls )
	{ 
		?>
		<div class="painel_de_controle">Selecionados:
		<?php
		/*
		 * Se este cadastro precisa de aprovação, mostra botão para aprovar usuário
		 */
/*
		if($precisa_approval['valor'] == '1'){ ?>
			<input type="submit" class="js_confirm" name="action[zero]" value="Zerar " />
			<?php
		}
*/
		/*
		 * Pode excluir?
		 */
//		if( StructurePermissions::getInstance()->canDelete($austNode) ){
			?>
			<input type="submit" class="js_confirm" name="action[subtract][1]" value="Subtrair 1" />
			<input type="submit" name="action[see_data_separated_by_semicolon][email]" value="Ver emails" />
			<?php
//		}
		?>
		</div>
	
		<br clear="all" />
	<?php } ?>

	<?php
	if(count($query['results']) == 0){
		?>
		<table cellspacing="0" cellpadding="0" border="0" class="listing">
		<tr class="list">
			<td colspan="1">
				<strong>Nenhum registro encontrado.</strong>
			</td>
		</tr>
		</table>
		<?php
	} else {
		if( empty($viewType) || $viewType == 'normal' )
			include($module->getIncludeFolder().'/view/mod/_view_table_list.php');
		else
			include($module->getIncludeFolder().'/view/mod/_view_'.$viewType.'.php');

	}
	?>

</form>

<br />
<p>
	<a href="javascript: history.back()"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
