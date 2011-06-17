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
    $moduloConfig = $modulo->loadModConf($params);

	$editorPlugins = '';
	if( $modulo->getStructureConfig('upload_inline_images') == '1' )
		$editorPlugins = 'imagemanager';
	
    $modulo->loadHtmlEditor($editorPlugins);


/*
 * Ajusta variáveis iniciais
 */
    $austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';

/*
 * [Se novo conteúdo]
 */
    if($_GET['action'] == 'create'){
        $tagh2 = "Criar: ". $this->Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node']);
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



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data" >
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">

<?php if($_GET['action'] == 'create'){ ?>
    <input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
<?php } else { ?>
    <input type="hidden" name="frmautor" value="<?php ifisset( $dados['autor'] );?>">
<?php }?>

<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">

<table cellpadding=0 cellspacing=0 class="form">
	
    <?php if( $modulo->isEdit() && $modulo->getStructureConfig("show_visits_counter") ){ ?>
	<tr>
	    <td><label>Estatísticas:</label></td>
	    <td>
			<?php
			if( $dados['visitantes'] == 0 ){
				?>
				Nenhum visitante viu este conteúdo até agora.
				<?php
			} else if( $dados['visitantes'] == 1 ){
				?>
	         	Apenas <strong>um</strong> visitante viu este conteúdo.
				<?php
			} else if( $dados['visitantes'] > 1 ){
				?>
	         	Este conteúdo foi visto por <strong><?php echo $dados['visitantes']?></strong> 
				visitantes.
				<?php
			}
			?>
	    </td>
	</tr>
	<?php } ?>

	<?php
	$slave = Aust::getInstance()->getRelatedSlaves($_GET['aust_node']);
	if( !empty($slave) && $modulo->isEdit() ){
		$slave = reset($slave);
		?>	
	    <tr>
	        <td valign="top"><label>Opções:</label></td>
	        <td valign="top">
				<?php
				foreach ($slave as $key => $value) {
					?>
					<div>
		            <a href="adm_main.php?section=conteudo&action=edit&aust_node=<?php echo $value['slave_id']?>&related_master=<?php echo $_GET['aust_node']?>&related_w=<?php echo $_GET['w']?>">
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
    <tr>
        <td class="first"><label>Categoria:</label></td>
        <td class="second">
            <div id="categoriacontainer">
            <?php
            $current_node = '';
            if( $_GET['action'] == "editar" || $_GET['action'] == "edit" ){
                $current_node = $dados['categoria'];
                ?>
                <input type="hidden" name="frmcategoria" value="<?php echo $current_node; ?>">
                <?php
            }

            echo BuildDDList( Registry::read('austTable') ,'frmcategoria', User::getInstance()->tipo ,$austNode, $current_node);
            ?>


            </div>
            <?php
            /*
             * Nova_Categoria?
             */
            $showNovaCategoria = false;
            if( !empty($moduloConfig["nova_categoria"]) ){
                if( $moduloConfig["nova_categoria"]["valor"] == "1" )
                    $showNovaCategoria = true;
            }
            if( $showNovaCategoria ){
                lbCategoria($austNode);
            }
            ?>
        </td>
    </tr>
	<tr>
	    <td><label>Título:</label></td>
	    <td>
	        <INPUT TYPE='text' NAME='frmtitulo' class='text' value='<?php if( !empty($dados['titulo']) ) echo $dados['titulo'];?>' />
	        <p class="explanation">

	        </p>
	    </td>
	</tr>
	
    <?php
    /*
     * PREVIEW URL
     */
    if( $modulo->getStructureConfig("has_file") ){ ?>
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
    if( $modulo->isEdit() AND $modulo->getStructureConfig("generate_preview_url") ){ ?>
    <tr>
        <td valign="top"><label>URL gerada:</label></td>
        <td>
            <?php echo $modulo->getGeneratedUrl(); ?>
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

    if( $modulo->getStructureConfig("resumo") ){
    ?>
    <tr>
        <td valign="top"><label>Resumo:</label></td>
        <td>
            <textarea name="frmresumo" rows="2"><?php if( !empty($dados['resumo']) ) echo $dados['resumo'];?></textarea>
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
    if( 1 == 0 AND $modulo->getStructureConfig("manual_date") ){
    ?>
    <tr>
        <td valign="top"><label>Data manual:</label></td>
        <td>
		    <input type="text" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
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
            <textarea name="frmtexto" id="jseditor"><?php if( !empty($dados['texto']) ) echo $dados['texto'];?></textarea>
        <br />
        </td>
    </tr>
    <?php
    /*
     * ORDEM
     */
    $showModo = false; // por padrão, não mostra
    if( !empty($moduloConfig["modo_de_visualizacao"]) ){
        if( $moduloConfig["modo_de_visualizacao"]["valor"] == "1" )
            $showModo = true;
    }
    if( $showModo ){
    ?>
        <tr>
            <td valign="top"><label>Modo:</label></td>
            <td>
                <select name="frmrestrito" class="select">
                    <option <?php if( !empty($dados['restrito']) ) makeselected($dados['restrito'], 'normal'); ?> value="normal">Mostrar em todas as páginas</option>
                    <option <?php if( !empty($dados['restrito']) ) makeselected($dados['restrito'], 'naofrontend'); ?> value="naofrontend">Não mostrar na página principal</option>
                    <option <?php if( !empty($dados['restrito']) ) makeselected($dados['restrito'], 'invisivel'); ?> value="invisivel">Tornar invisível este item em todo o site</option>
                </select>
                <p class="explanation">
                    Selecione acima que tipo de exibição você deseja para este conteúdo.
                </p>
            </td>
        </tr>
        <?php
    }
    ?>

    <?php
    /*
     * EMBED
     * mostra <input> de módulos embed
     *
     * Embed significa que os <input>s aqui mostrados serão enviados juntamente
     * com o <form> principal
     *
     * O arquivo inserido é /embed/form.php do módulo que $embed==true
     */

        include(INC_DIR.'conteudo.inc/form_embed.php');

    ?>
    <tr>
        <td colspan="2"><center><INPUT TYPE="submit" VALUE="Enviar!" name="submit" class="submit"></center></td>
    </tr>
</table>

</form>


<?php
    /*
     * EMBED OWN FORM
     * mostra <input> de módulos embedownform
     *
     * Embed Own Form significa que o formulário possui a própria tag <form>, não
     * dependendo do <form> principal
     *
     * É padrão e pode ser copiado para todos os forms
     */

        include(INC_DIR.'conteudo.inc/form_embedownform.php');


?>


<br />
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
