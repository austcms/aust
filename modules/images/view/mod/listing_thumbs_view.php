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
				<a href="adm_main.php?section=<?php echo CONTENT_DISPATCHER ?>&action=edit&aust_node=<?php echo $_GET['aust_node']?>&w=<?php echo $value["id"]?>">

				<?php
			}
			$randomNumber = rand(0,10000);
			?>
			<div class="image" style="background-image: url(<?php echo IMAGE_VIEWER_DIR?>visualiza_foto.php?table=images&thumbs=yes&myid=<?php echo $value["id"]; ?>&minxsize=<?php echo $thumbsW?>&minysize=<?php echo $thumbsH?>&r=<?php echo $randomNumber?><?php echo $fromFile; ?>)">
			</div>
			<?php
			if( StructurePermissions::getInstance()->canEdit($_GET['aust_node']) ){ 
				?>
				</a>
				<?php
			}
			?>
			<div class="info">
				<input type="checkbox" name="itens[]" value="<?php echo $value['id'];?>" />
				<?php echo $value['title'] ?>
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