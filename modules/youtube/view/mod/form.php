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


/*
 * Ajusta variáveis iniciais
 */
	$austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
	$w = (!empty($_GET['w'])) ? $_GET['w'] : '';

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
					*
				FROM
					".$module->useThisTable()."
				WHERE
					id='$w'
				";
		$query = $module->connection->query($sql);
		$dados = $query[0];
	}
?>
<h2><?php echo $tagh1;?></h2>
<p><?php echo $tagp;?></p>



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data" >
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">
<?php if($_GET['action'] == 'create'){ ?>
	<input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
	<input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
<?php } else { ?>

	<input type="hidden" name="frmadddate" value="<?php ifisset( $dados['adddate'] );?>">
	<input type="hidden" name="frmautor" value="<?php ifisset( $dados['autor'] );?>">

<?php }?>
<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">
<table border=0 cellpadding=0 cellspacing=0 class="form">

	<tr>
		<td colspan="2">
			<?php
			if( !empty($dados['url']) AND
				substr($dados['url'], 0, 7) == 'http://' )
			{
				//http://www.youtube.com/watch?v=l9-7HaVKHaE&feature=popular
				//http://www.youtube.com/v/l9-7HaVKHaE&hl=pt_BR&fs=1&rel=0
				$showUrl = str_replace("/watch?v=", "/v/", $dados['url']).'&hl=pt_BR&fs=1&rel=0';
				?>
				<center>
				<object width="480" height="295"><param name="movie" value="<?php echo $showUrl?>"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="<?php echo $showUrl?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="295"></embed></object>
				</center>
				<?php
			}
			?>
		</td>
	</tr>

	<?php
	/*
	 * CATEGORIA
	 *
	 * Se não deve-se configurar a categoria, a categoria padrão é a mesma que
	 * aust_node
	 */
	$showCategoria = false;
	if( !empty($moduloConfig["categoria"]) ){
		if( $moduloConfig["categoria"]["valor"] == "1" )
			$showCategoria = true;
	}
	if( $module->getStructureConfig("categorias") ){
	?>

		<tr>
			<td valign="top" class="first"><label>Categoria:</label></td>
			<td class="second">
				<div id="categoriacontainer">
				<?php
				$current_node = '';
				if($_GET['action'] == "editar"){
					$current_node = $dados['categoria'];
					?>
					<input type="hidden" name="frmcategoria" value="<?php echo $current_node; ?>">
					<?php
				}

				echo BuildDDList( Registry::read('austTable') ,'frmcategoria', User::getInstance()->tipo ,$austNode, $current_node);
				?>
				</div>

			</td>
		</tr>
		<?php
	} else {
		?>
		<input type="hidden" name="frmcategoria" value="<?php echo $austNode ?>" />
		<?php
	}
	?>
	<tr>
		<td valign="top" class="first"><label>Título:</label></td>
		<td class="second">
			<INPUT TYPE='text' NAME='frmtitulo' class='text' value='<?php if( !empty($dados['titulo']) ) echo $dados['titulo'];?>' />
			<p class="explanation">

			</p>
		</td>
	</tr>
	<tr>
		<td valign="top"><label>URL:</label></td>
		<td>
			<INPUT TYPE='text' NAME='frmurl' class='text' value='<?php if( !empty($dados['url']) ) echo $dados['url'];?>' />
			<p class="explanation">
				Endereço do vídeo no YouTube. (Ex.: http://www.youtube.com/watch?v=Z00jjc-WtZI)
			</p>
		</td>
	</tr>

	<?php
	/*
	 * RESUMO
	 */
	$showResumo = false;
	if( !empty($moduloConfig["summary"]) ){
		if( $moduloConfig["summary"]["valor"] == "1" )
			$showResumo = true;
	}
	if( $showResumo ){
	?>
	<tr>
		<td valign="top"><label>Resumo:</label></td>
		<td>
			<INPUT TYPE='text' NAME='frmresumo' class='text' value='<?php if( !empty($dados['summary']) ) echo $dados['summary'];?>' />
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
	if( !empty($moduloConfig["ordenate"]) ){
		if( $moduloConfig["ordenate"]["valor"] == "1" )
			$showOrdem = true;
	}
	if( $showOrdem ){
	?>
	<tr>
		<td valign="top"><label>Ordem:</label></td>
		<td>
			<select name="frmordem" class="select">
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '10'); ?> value="10">10</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '9'); ?> value="9">9</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '8'); ?> value="8">8</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '7'); ?> value="7">7</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '6'); ?> value="6">6</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '5'); ?> value="5">5</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '4'); ?> value="4">4</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '3'); ?> value="3">3</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '2'); ?> value="2">2</option>
				<option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '1'); ?> value="1">1</option>
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
	 * Texto
	 */
	$showTexto = false; // por padrão, não mostra
	if( !empty($moduloConfig["description"]) ){
		if( $moduloConfig["description"]["valor"] == "1" )
			$showTexto = true;
	}
	if( $showTexto ){
		?>
		<tr>
			<td colspan="2"><label>Texto: </label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea name="frmtexto" id="jseditor" rows="20" style="width: 670px"><?php if( !empty($dados['texto']) ) echo $dados['texto'];?></textarea>
			<br />
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
