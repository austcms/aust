<?php

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

		$files = $modulo->getFiles($params);
		
		if( !empty($files) ){
			$thumbsW = 80;
			$thumbsH = 80;
			$itemsPerLine = 3;
			$o = 0;
			
			/*
			 * LIGHTBOX
			 */
			?>					

			
			<div class="thumbs_view">
			<table width="100%">
			<?php
			$i = 0;
			foreach( $files as $key=>$file ){
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
				
				<img class="thumb" src="" />
				<br clear="all" />
				<span class="filename">
				<?php
				// $file['file_type'];
				// $file['file_size'];
				echo $file['original_file_name'];
				?>
				</span>
				<br clear="all" />
				
                <a href="javascript: void(0);" onclick="if( confirm('Você tem certeza que deseja excluir este arquivo?') ) window.open('adm_main.php?section=<?php echo $_GET["section"]; ?>&action=<?php echo $_GET["action"]; ?>&aust_node=<?php echo $_GET["aust_node"]; ?>&w=<?php echo $_GET["w"];?>&deletefile=<?php echo $file["id"]; ?>','_top');">
                    <img src="core/user_interface/img/icons/delete_15x15.png" alt="Excluir" title="Excluir" border="0" />
                </a>
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
		if( $modulo->getFieldConfig($fieldName, 'files_field_limit_quantity') != 1 )
			$multiple = 'multiple="multiple"';
		
		// somente uma imagem
		if(
			$i > $modulo->getFieldConfig($fieldName, 'files_field_limit_quantity') AND
			$modulo->getFieldConfig($fieldName, 'files_field_limit_quantity') > 0
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
?>