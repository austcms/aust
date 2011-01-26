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
        $tagh2 = "Criar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']);
        $tagp = 'Crie um novo conteúdo abaixo.';
        $dados = array('id' => '');
    }
/*
 * [Se modo edição]
 */
    else if($_GET['action'] == 'edit'){

    }
?>
<h2><?php echo $tagh2;?></h2>
<p><?php echo $tagp;?></p>

	<br clear="all" />
	<table cellspacing="0" cellpadding="0" border="0" class="listagem">
	<?php
	if(count($query) == 0){
	    ?>
	    <tr class="conteudo">
	        <td colspan="1">
	            <strong>Nenhum registro encontrado.</strong>
	        </td>
	    </tr>
	    <?php
	} else {

        include($modulo->getIncludeFolder().'/view/mod/_view_table_list.php');

	}
	?>
	</table>

</form>

<br />
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
