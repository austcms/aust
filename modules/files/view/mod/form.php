<?php
/*
 * FORM
 *
 * Formulário de inclusão de conteúdo
 */

// $fm = form_method = gravar, editar, etc, pois é o mesmo formulário para fins diferentes
$austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';

/*
 * Carrega configurações automáticas do DB
 */
	$params = array(
		"aust_node" => $_GET["aust_node"],
	);
	$moduloConfig = $module->loadModConf($params);


if($_GET['action'] == 'edit'){
	$h1 = 'Editar: '.$this->module->name;
	$sql = "
			SELECT
				*
			FROM
				".$this->module->useThisTable()."
			WHERE
				id='".$_GET['w']."'
			";
	$mysql = $module->connection->query($sql);
	$dados = $mysql[0];
	$fm = "edit";
} else {
	$h1 = 'Novo arquivo';
	$fm = "create";
}

/*
 * Tamanho máximo do Upload.
 */
$maxSize = (int) str_replace('M','', ini_get('upload_max_filesize') );
if( (int) str_replace('M','', ini_get('post_max_size') ) < $maxSize )
	$maxSize = (int) str_replace('M','', ini_get('post_max_size') );

?>

<h2><?php echo $h1?></h2>

<p>Envie um arquivo para o site.</p>

<form method="POST" action="adm_main.php?section=<?php echo $_GET['section'];?>&action=save&aust_node=<?php echo $_GET['aust_node']?>" enctype="multipart/form-data">
	<input type="hidden" name="method" value="<?php echo $_GET['action'];?>">

	<input type="hidden" name="w" value="<?php if( !empty($_GET['w']) ) echo $_GET['w']; ?>">
	<input type="hidden" name="aust_node" value="<?php echo $austNode;?>">
	<input type="hidden" name="frmnode_id" value="<?php echo $austNode;?>">
	<input type="hidden" name="frmcreated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
	<input type="hidden" name="frmfile_name" value="<?php if( !empty($dados['file_name']) ) echo $dados['file_name'];?>">
	<input type="hidden" name="frmfile_type" value="<?php if( !empty($dados['file_type']) ) echo $dados['file_type'];?>">
	<input type="hidden" name="frmfile_size" value="<?php if( !empty($dados['file_size']) ) echo $dados['file_size']; ?>">

	<input type="hidden" name="frmurl" value="<?php if( !empty($dados['url']) ) echo $dados['url']; ?>">
	<input type="hidden" name="frmadmin_id" value="<?php if( !empty($dados['admin_id']) ) echo $dados['admin_id']; else echo User::getInstance()->LeRegistro('id');?>">
	
	<table class="form">
	<?php
	/*
	 * CATEGORIA
	 */
	if( $module->getStructureConfig('select_category') == 1 ){
		?>
		<tr>
			<td valign="top">Selecione a categoria: </td>
			<td>
				<div id="categoriacontainer">
				<?php
				$current_node = $austNode;
				if($fm == "edit"){
					$current_node = $dados['node_id'];
				}
				echo BuildDDList( Registry::read('austTable'), 'frmnode_id', 0, $austNode, $current_node );
				?>
				</div>
				<?php
				/*
				 * Nova_Categoria?
				 */
				if( $module->getStructureConfig('new_aust_node') == 1 ){
					$param = array(
						'austNode' => $austNode,
						'categoryInput' => 'frmnode_id',
					);
					lbCategoria( $param );
				}
				?>

			</td>
		</tr>
	<?php
	} else {
		if($fm == "edit"){
			$current_node = $dados['node_id'];
		} else {
			$current_node = $austNode;
		}
		?>
		<input type="hidden" name="frmnode_id" value="<?php echo $current_node; ?>">
		<?php
	}
	?>

	<tr>
		<td valign="top" class="first">
			<label>
			<?php if($fm == "edit"){ ?>
				Arquivo:
			<?php }else{ ?>
				Selecione o arquivo:
			<?php } ?>
			</label>
		</td>
		<td class="second">
			<?php
			/*
			 * EDIT: filename
			 */
			if( $fm == "edit"){
				?>
				<img src="<?php echo getFileIcon($dados['file_name']);?>" align="left" />
				<span style="position: relative; left: 7px; top: 4px; display: block">
					<strong>
					<?php
					if( empty($dados['original_filename']) )
						echo lineWrap($dados['file_name'], 64);
					else
						echo lineWrap($dados['original_filename'], 64);
					?>
					</strong>
					<br />
					<span class="filesize">
						<?php echo convertFilesize( $dados['file_size'] ); ?>Mb
					</span>
				</span>
				<br />
				<?php
			} else {
				?>
				<input type="file" name="arquivo">
				<p class="explanation">
					Localize o arquivo que você deseja realizar upload.
					O tamanho máximo aceito neste servidor é <?php echo $maxSize; ?>Mb.
				</p>
			<?php } ?>
		</td>
	</tr>
	<?php
	/**
	 * PATH DO ARQUIVO PARA LINK?
	 */
	if($fm == "edit"){
		$showshow_path_to_link = false; // por padrão, não mostra
		if( !empty($moduloConfig["show_path_to_link"]) ){
			if( !empty($moduloConfig["show_path_to_link"]["valor"])
				&& $moduloConfig["show_path_to_link"]["valor"] == "1" )
				$showshow_path_to_link = true;
		}
		if( $showshow_path_to_link ){

			$url = '';

			
			if( !empty($dados['url']) ){
				if( strtolower( substr($_SERVER["SERVER_PROTOCOL"], 0, 4) ) == 'http' ){
					$url = 'http://';
				}
				$url = $module->parseUrl( $url.$_SERVER["SERVER_NAME"].$dados['url'] );

			}
			?>
			<tr>
				<td valign="top" class="first"><label>Endereço do arquivo:</label></td>
				<td class="second">
					<?php echo $url;?>
					<p class="explanation">
						Copie, se desejar, para criar links para o arquivo.
					</p>
				</td>
			</tr>
			<?php
		}
	}
	?>
	<tr>
		<td valign="top" class="first"><label>Título:</label></td>
		<td class="second">
			<input type="text" name="frmtitle" value="<?php if( !empty($dados['title']) ) echo $dados['title'];?>" class="text" />
			<?php tt('Digite um título. Lembre-se, títulos começam com letra maiúscula e não leva
				ponto final.'); ?>
			<p class="explanation">
				Exemplo: <em>Arquivo de exercícios segunda prova</em>
			</p>
			<p class="explanation" id="exists_titulo">
			</p>
		</td>
	</tr>
	<?php
	/*
	 * DESCRICAO
	 */
	$showDescricao = false; // por padrão, não mostra
	if( !empty($moduloConfig["description"]) ){
		if( !empty($moduloConfig["description"]["valor"])
			&& $moduloConfig["description"]["valor"] == "1" )
			$showDescricao = true;
	}
	if( $showDescricao ){
		?>
		<tr>
			<td valign="top"><label>Descrição:</label></td>
			<td>
				<textarea name="frmdescription" id="jseditor" rows="8" cols="45" style="font-size: 11px; font-family: verdana;"><?php ifisset( str_replace("\n","<br>",$dados['description']) ); // Para TinyMCE ?></textarea>
				<p class="explanation">
					Digite uma descrição para este arquivo.
				</p>
			</td>
		</tr>
		<?php
	}
	?>
	<tr>
		<td colspan="2"><center><input type="submit" value="Enviar" class="submit"></center></td>
	</tr>
	</table>

</form>

<br />
<p class="explanation">
	Os arquivos enviados poderão ter o tamanho limite de
	<strong><?php echo $maxSize ?>Mb</strong> neste servidor.
</p>


