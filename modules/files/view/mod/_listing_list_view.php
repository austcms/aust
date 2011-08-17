<table cellspacing="0" cellpadding="10" class="listing">
	<tr class="header">

		<?php for($i=0; $i< count($module->config['contentHeader']['campos']); $i++) { ?>
				<td class="<?php echo $module->config['contentHeader']['campos'][$i]; ?>">
					<?php
						echo $module->config['contentHeader']['camposNome'][$i];
					?>
				</td>
		<?php } ?>
		<td width="80" align="center">
			Op&ccedil;&otilde;es
		</td>
	</tr>
<?php
if(count($query) > 0){
	foreach($query as $dados){
	?>
		<tr class="list">
			<?php
			/*******************************
			*
			*
			*  LISTAGEM DO DB
			*
			*
			*******************************/
			for($i=0; $i< count($module->config['contentHeader']['campos']); $i++) { ?>
				<td>
					<?php
					if($i == 1){
						if( StructurePermissions::getInstance()->canEdit($_GET['aust_node']) )
							echo '<a href="adm_main.php?section='.$_GET['section'].'&action=edit&aust_node='.$_GET['aust_node'].'&w='.$dados["id"].'">';
						echo $dados[$module->config['contentHeader']['campos'][$i]];
						if( StructurePermissions::getInstance()->canEdit($_GET['aust_node']) )
							echo '</a>';
					} else {
						echo $dados[$module->config['contentHeader']['campos'][$i]];
					}
					?>
				</td>
			<?php } ?>
			<td align="center">
				<?php
				if( StructurePermissions::getInstance()->canDelete($austNode) ){
					?>
					<input type='checkbox' name='itens[]' value='<?php echo $dados['id'];?>'>
					<?php
				}
				?>
			</td>
		</tr>
	<?php
	} // FIM DO WHILE
} else {
	?>
	<tr>
		<td colspan="<?php echo count($content_header)+1;?>">
		<strong>Não há arquivos cadastrados.</strong>
		</td>
	</tr>
	<?php
}
?>
</table>