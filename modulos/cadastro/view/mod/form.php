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
$infoCadastro = $modulo->pegaInformacoesCadastro($austNode);
$tabelaCadastro = $infoCadastro["estrutura"]['tabela']["valor"];
$tabelaImagens = $infoCadastro["estrutura"]['table_images']["valor"];

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

<h2>Cadastro: <?php echo $this->aust->leNomeDaEstrutura($_GET['aust_node'])?></h2>
<?php
if( $_GET['action'] == "edit" ){
    if( $modulo->getStructureConfig("has_printing_version") ){
        ?>
        <a target="_blank" href="adm_main.php?section=<?php echo $_GET["section"] ?>&action=printing&theme=blank&aust_node=<?php echo $_GET['aust_node'] ?>&w=<?php echo $_GET['w'] ?>">
        Versão para impressão
        </a>
        <?
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
	echo $form->create( $infoCadastro["estrutura"]["tabela"]["valor"], $options );
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
							<a id="del_secondary_image" href="javascript: void(0);" data-secondaryid="" onclick="if( confirm('Você tem certeza que deseja excluir esta imagem?') ) window.open('adm_main.php?section=<?php echo $_GET["section"]; ?>&action=<?php echo $_GET["action"]; ?>&aust_node=<?php echo $_GET["aust_node"]; ?>&w=<?php echo $_GET["w"];?>&deleteimage='+$(this).attr('data-secondaryid'),'_top');">
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
echo $form->create( $infoCadastro["estrutura"]["tabela"]["valor"] );
/*
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?<?php echo $_SERVER['QUERY_STRING'];?>&action=gravar">
 * 
 */
?>
<input type="hidden" name="metodo" value="<?php echo $_GET["action"];?>" />
<input type="hidden" name="frmcreated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
<input type="hidden" name="frmautor" value="<?php echo $administrador->LeRegistro('id');?>">
<input type="hidden" name="w" value="<?php ifisset($_GET['w']);?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode;?>">


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
//	pr($camposForm);
foreach( $camposForm as $chave=>$valor ){

    unset($inputType);
    $select = array();
    $checkbox = array();
	$useInput = false;
	$class = "";
	$elementId = '';

    if( array_key_exists($valor['nomeFisico'], $divisorTitles) ){
        ?>
        <h3><?php echo $divisorTitles[$valor['nomeFisico']]['valor']; ?></h3>
        <?php
        if( !empty($divisorTitles[$valor['nomeFisico']]['comentario']) ){
            echo '<p>'.$divisorTitles[$valor['nomeFisico']]['comentario'].'</p>';
        }
		$useInput = true;
    }

    /**
     * RELACIONAL UM PARA UM
     */
    if( $valor["tipo"]["especie"] == "relacional_umparaum" ){
        $sql = "SELECT id,".$valor["tipo"]["tabelaReferenciaCampo"]." FROM ".$valor["tipo"]["tabelaReferencia"];
        $selectValues = $conexao->query($sql);
        //pr($sql);
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
        
        $referencia = $valor["tipo"]["tabelaReferencia"];
        $tabelaRelacional = $valor["tipo"]["referencia"];
        $campo = $valor["tipo"]["tabelaReferenciaCampo"];
        $sql = "SELECT
                    t.id, t.$campo
                FROM
                    ".$referencia." AS t
                ORDER BY t.$campo ASC
                ";
        $checkboxes = $modulo->connection->query($sql);

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

            $values = $modulo->connection->query($sql);
            if( empty($values)){
                $values = array();
            } else {
                foreach( $values as $id ){
                    $valor["valor"][] = $id["referencia"];
                }
            }
        }
		$useInput = true;

	}
    /*
     * IMAGES
     *
     * Fields for images field
     */
    else if($valor["tipo"]["especie"] == "images") {
	
		
		// nome físico do campo
		$fieldName = $valor['nomeFisico'];
		
		// nome do input
		$inputName = "data[".$infoCadastro["estrutura"]["tabela"]["valor"]."][".$fieldName."]";
		?>
		<div class="input">
	        <label for="input-<?php echo $fieldName ?>"><?php echo $valor['label'] ?></label>
	
	
	        <div class="input_field input_images input_<?php echo $fieldName ?>">
			
			<div class="images">
				<?php
				$params = array(
					'w' => $w,
					'field' => $fieldName,
					'austNode' => $austNode,
				);
				
				$images = $modulo->getImages($params);
				
				if( !empty($images) ){
					$thumbsW = 80;
					$thumbsH = 80;
					$itemsPerLine = 4;
					$o = 0;
					
					/*
					 * LIGHTBOX
					 */
					?>					
		
					
					<div class="thumbs_view">
					<table width="100%">
					<?php
					$randomNumber = rand(0,10000);
					
					$imagesPath = IMAGE_VIEWER_DIR."visualiza_foto.php?table=".$tabelaImagens."&fromfile=true&thumbs=yes&minxsize=". $thumbsW."&minysize=". $thumbsH."&r=".$randomNumber."&myid=";
					?>
					<script type="text/javascript">
					var imagesPath = '<?php echo $imagesPath ?>';
					</script>
					<?php
					$i = 0;
					foreach( $images as $key=>$image ){
						$o++;
						$i++;
						
						// somente uma imagem
						if(
							$i > $modulo->getFieldConfig($fieldName, 'image_field_limit_quantity') AND
							$modulo->getFieldConfig($fieldName, 'image_field_limit_quantity') > 0
						)
							break;
						
						if( $o == 1 ){
							?>
							<tr>
							<?php
						}
						?>
						<td>
						<script type="text/javascript">
						/* DEFINIÇÕES DE CAMPOS IMAGES */
						imageHasDescription['image_<?php echo $image['id']?>'] = "<?php echo $modulo->getFieldConfig($fieldName, 'image_field_has_description')?>";
						imageHasLink['image_<?php echo $image['id']?>'] = "<?php echo $modulo->getFieldConfig($fieldName, 'image_field_has_link')?>";
						imageHasSecondaryImage['image_<?php echo $image['id']?>'] = "<?php echo $modulo->getFieldConfig($fieldName, 'image_field_has_secondary_image')?>";
						</script>
						
						<img class="thumb" name="image_<?php echo $image['id']?>" src="<?php echo $imagesPath.$image['id']?>" />
						<input type="hidden" name="image_description_<?php echo $image['id'] ?>" value="<?php echo $image['description'] ?>" />
						<input type="hidden" name="image_link_<?php echo $image['id'] ?>" value="<?php echo $image['link'] ?>" />
						<input type="hidden" name="image_secondaryid_<?php echo $image['id'] ?>" value="<?php echo $image['secondaryid'] ?>" />
						<br clear="all" />
                        <a href="javascript: void(0);" onclick="if( confirm('Você tem certeza que deseja excluir esta imagem?') ) window.open('adm_main.php?section=<?php echo $_GET["section"]; ?>&action=<?php echo $_GET["action"]; ?>&aust_node=<?php echo $_GET["aust_node"]; ?>&w=<?php echo $_GET["w"];?>&deleteimage=<?php echo $image["id"]; ?>','_top');">
                            <img src="core/user_interface/img/icons/delete_15x15.png" alt="Excluir" title="Excluir" border="0" />
                        </a>
						<?php
						if( $modulo->getFieldConfig($fieldName, 'image_field_has_description') OR
							$modulo->getFieldConfig($fieldName, 'image_field_has_link') OR
						 	$modulo->getFieldConfig($fieldName, 'image_field_has_secondary_image') )
						{
							?>
							<a href="javascript: void(0)" class="lightbox-panel" id="image_<?php echo $image['id'] ?>" name="modal" onclick="editImageInLightbox(this, <?php echo $image['id'] ?>, '<?php echo $fieldName ?>')">
	                        	<img src="core/user_interface/img/icons/add_thumb_16x16.png" alt="Editar Informações" title="Editar Informações" border="0" />
							</a>
							<?php
						}
						?>
						</td>
						
						<?php
						if( $o == $itemsPerLine ){
							?>
							</tr>
							<?php
							$o = 0;
						}
					}

					if( $o < $itemsPerLine ){
						for( $i = 0; $i < ($itemsPerLine-$o); $i++ ){
							?>
							<td></td>
							<?php
						}
						?>
						</tr>
						<?php
					}
					?>
					</table>
					</div>
					<?php
				}
				?>
			</div>
			
			<?php
			for($i = 1; $i <= 3; $i++){

				$multiple = '';
				if( $modulo->getFieldConfig($fieldName, 'image_field_limit_quantity') != 1 )
					$multiple = 'multiple="multiple"';
				
				// somente uma imagem
				if(
					$i > $modulo->getFieldConfig($fieldName, 'image_field_limit_quantity') AND
					$modulo->getFieldConfig($fieldName, 'image_field_limit_quantity') > 0
				)
					break;
				
				?>
		        <input type="file" name="<?php echo $inputName ?>[]" value="<?php echo $inputValue ?>" id="input-<?php echo $fieldName ?>" <?php echo $multiple ?> />
				<?php
			}
			?>

			<br />
			<p class="explanation"><?php echo $valor['comentario'] ?></p>
			<br />
			</div>
		</div>
		<?php
		$useInput = false;
		
    } elseif( $valor['tipo']['tipoFisico'] == 'date' ){
        $inputType = "date";
		$useInput = true;
    } elseif( $valor['tipo']['tipoFisico'] == 'text' ){
        $inputType = "textarea";
		
		if( $modulo->getFieldConfig($chave, 'text_has_editor') == "1" ){
			$elementId = 'input-'.$chave;
			$elementsEditor[] = $elementId;
		}
		
		$useInput = true;
    } else {
		$useInput = true;
	}

    if( empty($valor["valor"]) ){
        $valor["valor"] = "";
    }


    if( empty($inputType) ){
        $inputType = "";
    }

	/*
	 * $form->input é uma forma automática de criar inputs. Campos do
	 * tipo images não precisa desta técnica, pois são diferentes.
	 */
	if( $useInput ){
	    /**
	     * Cria INPUT
	     */
	    echo $form->input( $chave, array(
	                                    "label" => $valor["label"],
	                                    "select" => $select,
	                                    "checkbox" => $checkbox,
	                                    "value" => $valor["valor"],
	                                    "type" => $inputType,
										'after' => '<p class="explanation">'.$valor['comentario'].'</p>'
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
	
	$params = array(
		'elements' => $elementsEditor
	);
	loadHtmlEditor($params);


echo $form->end();
?>
