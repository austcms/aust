<div id="thumbs_view">
	
<?php
if(count($query) == 0){
	?>
	<strong>Nenhum registro encontrado.</strong>
	<?php
} else {
	?>	
	<table width="100%">
	<?php

	$thumbsW = 100;
	$thumbsH = 100;
	$perLine = 5;
	$i = 0;

	foreach( $query as $key=>$value){
		
		$fromFile = '';
		if( !empty($value['file_systempath']) )
			$fromFile = '&fromfile=1';
		
		if( $i == 0 ){
			?>
			<tr>
			<?php
		}
		?>
		<td valign="top">
		<span class="item">
			<?php
			if( StructurePermissions::getInstance()->canEdit($_GET['aust_node']) ){ 
				?>
				<a href="adm_main.php?section=<?php echo $_GET['section']?>&action=edit&aust_node=<?php echo $_GET['aust_node']?>&w=<?php echo $value["id"]?>">

				<?php
			}
			$randomNumber = rand(0,10000);
			if( empty($value['original_filename']) )
				$value['original_filename'] = $value['arquivo_nome'];
			?>
			<img src="<?php echo getFileIcon($value['original_filename']);?>" title="<?php echo $value['original_filename']?>" />
			<?php
			if( StructurePermissions::getInstance()->canEdit($_GET['aust_node']) ){ 
				?>
				</a>
				<?php
			}
			?>
			<div class="info">
				<?php
				if( StructurePermissions::getInstance()->canDelete($austNode) ){
					?>
					<input type="checkbox" name="itens[]" value="<?php echo $value['id'];?>" />
					<?php
				}
				?>
				<?php
				if( !empty($value['titulo']) )
					$title = $value['titulo'];
				else if( !empty($value['original_filename']) )
					$title = $value['original_filename'];
				else
					$title = $value['arquivo_nome'];
					
				echo lineWrap($title);
				?>
			</div>
		</span>
		</td>
		<?php
		$i++;
		if($i == $perLine){
			?>
			</tr>
			<?php
			$i=0;
		}
	}
	if( $i < $perLine AND $i != 0 ){
		for( $o = $i; $i<$perLine; $i++ ){
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
	
	<?php
}	
?>
</div>