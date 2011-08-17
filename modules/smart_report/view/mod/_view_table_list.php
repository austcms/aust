<table cellspacing="0" cellpadding="0" border="0" class="listing">
<tr class="header">
	<?php foreach( $query['results'][0] as $field=>$value ){
		if( $field == '_id') continue;
	 	?>
	<td class="">
		<?php echo $field ?>
	</td>
	<?php } ?>

	<?php if( $this->showControls ){ ?>
	<td width="25" align="center">
		<input type='checkbox' class="check_checkboxes" data-target="class" checked="false" value='<?php echo $item['_id'];?>'>
	</td>
	<?php } ?>
	
</tr>
<?php foreach($query['results'] as $item){ ?>
	<tr class="list">
		<?php foreach( $item as $field=>$value ){
			if( $field == '_id') continue;
			?>
		<td>
			<?php echo $value; ?>
		</td>
		<?php } ?>

		<?php if( $this->showControls ){ ?>
		<td align="center">
			<?php
//			if( StructurePermissions::getInstance()->canDelete($austNode) ){
				
				$checked = "";
				if( !empty($_SESSION['selected_items'][$austNode]) &&
					in_array($item['_id'], $_SESSION['selected_items'][$austNode]['items'])
				)
					$checked = 'checked="checked"';
				?>
				<input type='checkbox' name="itens[]" <?php echo $checked; ?> value='<?php echo $item['_id'];?>'>
				<?php
//			}
			?>
		</td>
		<?php } ?>
		
	</tr>
<?php } ?>
</table>