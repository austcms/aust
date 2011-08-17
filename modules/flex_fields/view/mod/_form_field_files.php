<?php

// nome físico do campo
$fieldName = $value['nomeFisico'];

// nome do input
$inputName = "data[".$infoCadastro["structure"]["table"]["value"]."][".$fieldName."]";
?>
<div class="input">
	<label for="input-<?php echo $fieldName ?>"><?php echo $value['label'] ?></label>

	<div class="input_field input_images input_<?php echo $fieldName ?>">
	
	<div class="images">
		<?php
		$params = array(
			'w' => $w,
			'field' => $fieldName,
			'austNode' => $austNode,
		);

		$files = $module->getFiles($params);
		
		if( !empty($files) ){
			$thumbsW = 80;
			$thumbsH = 80;
			$itemsPerLine = 3;
			$o = 0;
			
			/*
			 * LIGHTBOX
			 */
			?>					

			
			<div class="files_view">
			<table width="100%">
			<?php
			$i = 0;
			foreach( $files as $key=>$file ){
				$o++;
				$i++;
				
				// somente uma imagem
				if(
					$i > $module->getFieldConfig($fieldName, 'image_field_limit_quantity') AND
					$module->getFieldConfig($fieldName, 'image_field_limit_quantity') > 0
				)
					break;
				
				if( $o == 1 ){
					?>
					<tr>
					<?php
				}
				?>
				<td valign="top" class="icon">
				
				<a href="<?php echo retrieveFile($file['file_systempath'], $file['file_type'], $file['original_file_name']) ?>">
				<img class="thumb" src="<?php echo getFileIcon($file['original_file_name']) ?>" />
				</a>
				<br clear="all" />
				<span class="filename">
				<?php echo $file['original_file_name']; ?>
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
		if( $module->getFieldConfig($fieldName, 'files_field_limit_quantity') != 1 )
			$multiple = 'multiple="multiple"';
		
		// somente uma imagem
		if(
			$i > $module->getFieldConfig($fieldName, 'files_field_limit_quantity') AND
			$module->getFieldConfig($fieldName, 'files_field_limit_quantity') > 0
		)
			break;
		
		?>
		<input type="file" name="<?php echo $inputName ?>[]" value="<?php echo $inputValue ?>" id="input-<?php echo $fieldName ?>" <?php echo $multiple ?> />
		<?php
	}
	?>

	<br />
	<p class="explanation"><?php echo $value['comentario'] ?></p>
	<br />
	</div>
</div>
<?php
$useInput = false;
?>