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
		
		$images = $module->getImages($params);
		
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
				<td>
				<script type="text/javascript">
				/* DEFINIÇÕES DE CAMPOS IMAGES */
				imageHasDescription['image_<?php echo $image['id']?>'] = "<?php echo $module->getFieldConfig($fieldName, 'image_field_has_description')?>";
				imageHasLink['image_<?php echo $image['id']?>'] = "<?php echo $module->getFieldConfig($fieldName, 'image_field_has_link')?>";
				imageHasSecondaryImage['image_<?php echo $image['id']?>'] = "<?php echo $module->getFieldConfig($fieldName, 'image_field_has_secondary_image')?>";
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
				if( $module->getFieldConfig($fieldName, 'image_field_has_description') OR
					$module->getFieldConfig($fieldName, 'image_field_has_link') OR
				 	$module->getFieldConfig($fieldName, 'image_field_has_secondary_image') )
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
		if( $module->getFieldConfig($fieldName, 'image_field_limit_quantity') != 1 )
			$multiple = 'multiple="multiple"';
		
		// somente uma imagem
		if(
			$i > $module->getFieldConfig($fieldName, 'image_field_limit_quantity') AND
			$module->getFieldConfig($fieldName, 'image_field_limit_quantity') > 0
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