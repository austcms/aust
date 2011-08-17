<?php $h1 = Aust::getInstance()->getStructureNameById($_GET['aust_node']); ?>
<h2><?php echo $h1; ?></h2>
<p>
	Abaixo você encontra a listagem dos últimos itens desta categoria.
</p>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET["aust_node"];?>">
<a name="list"></a>
<?php
/*
 * Permission to delete the content?
 */
if( StructurePermissions::getInstance()->canDelete($austNode) ){ ?>
	<div class="painel_de_controle"><input type="submit" class="js_confirm" name="deletar" value="Deletar selecionados" />
	</div>
<?php } ?>

<div id="listing_table">
	<?php
	include($module->getIncludeFolder().'/view/mod/_listing_thumbs_view.php');
	?>
</div>

</form>

<?php
/*
 * PAGINAÇÃO
 * mostra painel de navegação para paginação
 */

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