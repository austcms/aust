<?php
/**
 * Listagem dos dados cadastrados deste módulo. É carregado dinamicamente pelo
 * Core do Aust.
 *
 * @category Listagem
 * @author Alexandre de Oliveira <alexandreoliveira@gmail.com>
 * @since 
 */
?>
<div class="listagem">
<h2>
	Relatórios: <?php echo $h1;?>
</h2>
<p>
	Abaixo você encontra a listagem dos filtros deste relatório.
</p>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">
<a name="list"></a>

<br clear="all" />
<table cellspacing="0" cellpadding="0" border="0" class="listing">
	<tr class="header">
		<td class="">
			Filtros
		</td>
	</tr>
<?php
if(count($query) == 0){
	?>
	<tr class="list">
		<td colspan="1">
			<strong>Nenhum registro encontrado.</strong>
		</td>
	</tr>
	<?php
} else {
	foreach($query as $dados){
		?>
		<tr class="list">
			<td>
				
				<a href="adm_main.php?section=<?php echo $_GET['section'] ?>&action=view&aust_node=<?php echo $_GET['aust_node']?>&w=<?php echo $dados["id"]?>">
				<?php
				echo $dados['title'];
				?>
				</a>
				
			</td>
		</tr>
	<?php
	} // Fim do While
}
?>
</table>
</form>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
</div>