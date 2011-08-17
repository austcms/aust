	<div class="listagem">

	<h2>
		<?php echo $h1;?>
	</h2>
	<p>
		Abaixo você encontra a listagem dos últimos textos desta categoria.
	</p>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo MODULES;?>&action=actions&aust_node=<?php echo $austNode;?>">
	<a name="list"></a>
	<?php
	/* Is User Able to delete the content? */
	if( StructurePermissions::getInstance()->canDelete($austNode) ){
		?>
		<div class="painel_de_controle"><input type="submit" class="js_confirm" name="deletar" value="Deletar selecionados" /></div>
		<?php
	}
	?>

	<br clear="all" />
	<table cellspacing="0" cellpadding="0" border="0" class="listing">
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
	if(count($query) == 0){
		?>
		<tr class="list">
			<td colspan="<?php echo $i+1;?>">
				<strong>Nenhum registro encontrado.</strong>
			</td>
		</tr>
		<?php
	} else {
		foreach($query as $dados){
			?>
			<tr class="list">
				<?php
				for($i = 0; $i < count($module->config['contentHeader']['campos']); $i++) { ?>
					<td>
						<?php
						if($i == 1){

							if( StructurePermissions::getInstance()->canEdit($_GET['aust_node']) )
								echo '<a href="adm_main.php?section='.CONTENT_DISPATCHER.'&action=edit&aust_node='.$_GET['aust_node'].'&w='.$dados["id"].'">';
							echo $dados[$module->config['contentHeader']['campos'][$i]];
							if( StructurePermissions::getInstance()->canEdit($_GET['aust_node']) )
								echo '</a>';
						} else {
							echo $dados[$module->config['contentHeader']['campos'][$i]];
						}
						?>
					</td>
					<?php 
				}
				?>
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
		} // Fim do While
	}
	?>
	</table>
	</form>
	<?php
	/*
	 * PAGINAÇÃO
	 * mostra painel de navegação para paginação
	 */

		//$sql = $module->getSQLForListing($categorias);
		$total_registros = $module->totalRows;

		$total_paginas = $total_registros/$numPorPagina;
		$prev = $page - 1;
		$next = $page + 1;
		// se página maior que 1 (um), então temos link para a página anterior
		if ($page > 1) {
			$prev_link = ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$prev.'">Anterior</a>';
		} else { // senão não há link para a página anterior
			$prev_link = "Anterior";
		}
		// se número total de páginas for maior que a página corrente,
		// então temos link para a próxima página
		if ($total_paginas > $page) {
			$next_link = ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$next.'">Próxima</a>';
		} else {
		// senão não há link para a próxima página
			$next_link = "Próxima";
		}

		// vamos arredondar para o alto o número de páginas  que serão necessárias para exibir todos os
		// registros. Por exemplo, se  temos 20 registros e mostramos 6 por página, nossa variável
		// $total_paginas será igual a 20/6, que resultará em 3.33. Para exibir os  2 registros
		// restantes dos 18 mostrados nas primeiras 3 páginas (0.33),  será necessária a quarta página.
		// Logo, sempre devemos arredondar uma  fração de número real para um inteiro de cima e isto é
		// feito com a  função ceil()/
		$total_paginas = ceil($total_paginas);
		if($total_paginas > 1){
			$painel = "";
			for ($x=1; $x<=$total_paginas; $x++) {
				if ($x == $page) {
					// se estivermos na página corrente, não exibir o link para visualização desta página
					$painel .= " $x ";
				} else {
				   $painel .= ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$x.'">'.$x.'</a> ';
				}
			}
			// exibir painel na tela
			echo '<div class="paginacao"><strong>Navegação</strong>: '.$prev_link.' | '.$painel.' | '.$next_link.' </div>';
		}

	?>

</div>