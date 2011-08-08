<?php
/**
 * Formulário deste módulo
 *
 * @package ModView
 * @name form
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 09/07/2009
 */
/**
 * Informações deste cadastro
 */
$infoCadastro = $module->pegaInformacoesCadastro($austNode);
$tabelaCadastro = $infoCadastro["estrutura"]['tabela']["value"];

$tabelaImagens = null;
if( !empty($infoCadastro["estrutura"]['table_images']["value"]) )
	$tabelaImagens = $infoCadastro["estrutura"]['table_images']["value"];

/*
 * ...
 * 
 * TinyMCE é carregado ao final deste código
 *
 * ...
 */

$w = '';
if( !empty($_GET["w"]) ){
    $w = $_GET['w'];
}

//pr($infoCadastro);
?>

<h2>Cadastro: <?php echo Aust::getInstance()->getStructureNameById($_GET['aust_node'])?></h2>
<?php
if( $_GET['action'] == "edit" ){
    if( $module->getStructureConfig("has_printing_version") ){
        ?>
        <a target="_blank" href="adm_main.php?section=<?php echo $_GET["section"] ?>&action=printing&theme=blank&aust_node=<?php echo $_GET['aust_node'] ?>&w=<?php echo $_GET['w'] ?>">
        Versão para impressão
        </a>
        <?php
    }
}
?>
<p>
    <?php
    echo $formIntro;
    ?>
</p>

<?php
/*
 * LIGHTBOX
 *
 * Serve para inserir outras opções em imagens
 */
?>
	<?php
	
	$options = array(
		'action' => 'edit&aust_node='.$austNode.'&w='.$w,
	);
	echo $form->create( $infoCadastro["estrutura"]["tabela"]["value"], $options );
	?>
	<div id="lightbox-panel" class="window lb_images">
		<input type="hidden" name="type" value="image_options" />
		<input type="hidden" name="aust_node" value="<?php echo $austNode ?>" />
		<input type="hidden" name="w" value="<?php echo $w; ?>" />
		<input type="hidden" name="mainTable" value="<?php echo $tabelaCadastro ?>" />
	    <div class="header">
	        <h2>Propriedades da Imagem</h2>
	        <a href="#" class="close"></a>
	    </div>
	    <div class="lb_content">
	        <input type="hidden" name="image_id" value="" />
	        <table class="form">
	            <tr>
	                <td valign="top" class="titulo">
						Preview:
						<br />
	                    <img id="lb_image" style="margin-right: 15px" />
	                </td>
	                <td>
						<div class="description">
							Descrição:
							<br />
	                        <input name="data[<?php echo $tabelaCadastro ?>][description]" id="image_description" class="text" />
						</div>
						<div class="link">
							Link:
							<br />
	                        <input name="data[<?php echo $tabelaCadastro ?>][link]" id="image_link" class="text" />
							<p class="explanation">
								Não se esqueça de inserir http:// no início.
							</p>
						</div>
						<div class="secondary_image">
						<div id="secondary_image_form">
							Nova Imagem Secundária:
							<br />
	                        <input type="file" name="data[<?php echo $tabelaCadastro ?>][secondary_image][]" />
							<input type="hidden" name="image_field" value="" />
						</div>
						<div id="secondary_image_actual">
							Imagem secundária atual:
							<br />
							<img src="" name="secondary_image" />
							<a id="del_secondary_image" href="javascript: void(0);" data-secondaryid=""
								onclick="if( confirm('Você tem certeza que deseja excluir esta imagem?') ) window.open('adm_main.php?section=<?php echo $_GET["section"]; ?>&action=<?php echo $_GET["action"]; ?>&aust_node=<?php echo $_GET["aust_node"]; ?>&w=<?php echo $_GET["w"];?>&deleteimage='+$(this).attr('data-secondaryid'),'_top');">
	                            <img src="core/user_interface/img/icons/delete_15x15.png" alt="Excluir" border="0" />
	                        </a>
	                        
						</div>
						<p id="missing_secondary_image" class="display: none;">
							<em>Não há uma imagem secundária cadastrada.</em>
						</p>
						</div>
						
	                </td>
	            </tr>
	            <tr>
	                <td colspan="2">
	                <center>
	                    <button name="submit_category">
	                        Salvar
	                    </button>
	                </center>
	                </td>
	            </tr>
	        </table>

	    </div>
	    <div class="footer">
	    </div>
	</div>
	</form>

<?php
echo $form->create( $infoCadastro["estrutura"]["tabela"]["value"] );
/*
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?<?php echo $_SERVER['QUERY_STRING'];?>&action=gravar">
 * 
 */
$nodeIdFieldName = 'data['.$infoCadastro["estrutura"]["tabela"]["value"].'][node_id]';
?>
<input type="hidden" name="metodo" value="<?php echo $_GET["action"];?>" />
<input type="hidden" name="frmcreated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
<input type="hidden" name="frmautor" value="<?php echo User::getInstance()->LeRegistro('id');?>">
<input type="hidden" name="w" value="<?php ifisset($_GET['w']);?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode;?>">

<?php
if( $module->getStructureConfig("category_selectable") ){
	
    if( $_GET['action'] == EDIT_ACTION ){
        ?>
        <input type="hidden" name="<?php echo $nodeIdFieldName; ?>" value="<?php echo $nodeId; ?>">
        <?php
    }
	?>
	<div class="input"><label for="input-teste" class="select_category">Categoria</label><div class="input_field input_select">
	<?php
	if( empty($nodeId) ){
		$nodeId = false;
	}
	
    echo BuildDDList( Registry::read('austTable') , $nodeIdFieldName, User::getInstance()->tipo ,$austNode, $nodeId);
	?>
	<div class="after category">
		<?php
		/*
		 * Nova_Categoria?
		 */
		if( $module->getStructureConfig("category_creatable") ){

			if( empty($nodeId) )
				$nodeId = $austNode;
		    lbCategoria(array('austNode'=>$nodeId, 'categoryInput' => $nodeIdFieldName) );
		}
	
		?>
	</div>
	</div></div>
	<?php
	
}
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
        echo $form->input( "id", array(
                                        "type" => "hidden",
                                        "value" => $_GET["w"],
                                    )
                            );
    }

/**
 * Campos
 */
//pr($infoCadastro);

/*
 *
 * FORMULÁRIO DE CADASTRO
 *
 * O formulário é criado automaticamente
 *
 */
foreach( $camposForm as $chave=>$valor ){

    unset($inputType);
    $select = array();
    $checkbox = array();
	$useInput = false;
	$fieldClass = array();
	$elementId = '';

    if( array_key_exists($valor['nomeFisico'], $divisorTitles) ){
        ?>
        <h3><?php echo $divisorTitles[$valor['nomeFisico']]['valor']; ?></h3>
        <?php
        if( !empty($divisorTitles[$valor['nomeFisico']]['commentary']) ){
            echo '<p>'.$divisorTitles[$valor['nomeFisico']]['commentary'].'</p>';
        }
		$useInput = true;
    }

    /**
     * RELACIONAL UM PARA UM
     */
    if( $valor["tipo"]["especie"] == "relacional_umparaum" ){
        $sql = "SELECT id, ".$valor["tipo"]["tabelaReferenciaCampo"]." FROM ".$valor["tipo"]["tabelaReferencia"] ." ORDER BY ".$valor["tipo"]["tabelaReferenciaCampo"] ." LIMIT 50";
        $selectValues = Connection::getInstance()->query($sql);

        //$select["selected"] = "3";
        $inputType = "select";
        foreach($selectValues as $tabelaReferenciaResult){
            $select["options"][ $tabelaReferenciaResult["id"] ] = $tabelaReferenciaResult[ $valor["tipo"]["tabelaReferenciaCampo"] ];
        }
		$useInput = true;

    }
    /*
     * RELACIONAL UM PARA MUITOS
     *
     * Monta checkboxes do campo que é do tipo relacional um-para-muitos
     */
    else if($valor["tipo"]["especie"] == "relacional_umparamuitos") {

        include($module->getIncludeFolder().'/view/mod/_form_field_relational_one_to_many.php');

	}
    /*
     * IMAGES
     *
     * Fields for images
     */
    else if($valor["tipo"]["especie"] == "files") {
	
        include($module->getIncludeFolder().'/view/mod/_form_field_files.php');

	}
    /*
     * FILES
     *
     * Fields for files
     */
    elseif( $valor["tipo"]["especie"] == "images" ){

        include($module->getIncludeFolder().'/view/mod/_form_field_images.php');

    } elseif( $valor['tipo']['tipoFisico'] == 'date' ){
        $inputType = "date";
		$useInput = true;
    } elseif( $valor['tipo']['tipoFisico'] == 'text' ){
        $inputType = "textarea";
		
		if( $module->getFieldConfig($chave, 'text_has_editor') == "1" ){
			$elementId = 'input-'.$chave;
			$elementsEditor[] = $elementId;
		}

		if( $module->getFieldConfig($chave, 'text_has_images') == "1" ){
			$plugins[] = 'imagemanager';
		}
		
		$useInput = true;
    } elseif( $valor["tipo"]["especie"] == "string" ){

		$currencyMask = $module->getFieldConfig($chave, 'currency_mask');
		// Boolean, creates <select>
		if( $module->getFieldConfig($chave, 'boolean_field') == "1" ){
			$inputType = "select";
	        $select["options"] = array(
				"1" => "Sim",
				"0" => "Não",
			);
		} elseif ( !empty($currencyMask) && !is_numeric($currencyMask) ){
			$fieldClass[] = "currency_field";
			if( empty($valor["value"]) )
				$valor["value"] = 0;
			$valor["value"] = Resources::numberToCurrency($valor["value"], $this->module->language());
		}
	
		$useInput = true;
    } else {
		$useInput = true;
	}

    if( empty($valor["value"]) || $valor["value"] == '' ){
        $valor["value"] = "";
    }

    if( empty($inputType) ){
        $inputType = "";
    }

	/*
	 * $form->input é uma forma automática de criar inputs. Campos do
	 * tipo images não precisa desta técnica, pois são diferentes.
	 */
	$after = false;
	if( !empty($valor['commentary']) )
		$after = '<p class="explanation">'.$valor['commentary'].'</p>';

	if( $useInput ){
	    /**
	     * Cria INPUT
	     */
	    echo $form->input( $chave, array(
	                                    "label" => $valor["label"],
	                                    "select" => $select,
	                                    "checkbox" => $checkbox,
	                                    "value" => (string) $valor["value"],
	                                    "type" => $inputType,
										'after' => $after,
										"class" => implode(" ", $fieldClass)
	                                )
	                        );
	    ?>
	    <?php
	}
}

/*
 * LOAD TINYMCE
 */

	if( empty($elementsEditor) )
		$elementsEditor = array();
	else
		$elementsEditor = implode(',', $elementsEditor);

	if( empty($plugins) )
		$plugins = array();
	else
		$plugins = implode(',', $plugins);

	$params = array(
		'elements' => $elementsEditor,
		'plugins' => $plugins
	);
	loadHtmlEditor($params);


echo $form->end();
?>
