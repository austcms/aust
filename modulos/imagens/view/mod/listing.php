<?php
/**
 * Listagem dos dados cadastrados deste módulo. É carregado dinamicamente pelo
 * Core do Aust.
 *
 * @package Módulo Texto
 * @category Listagem
 * @name Listar
 * @author Alexandre de Oliveira <alexandreoliveira@gmail.com>
 * @version v0.1
 * @since 
 */
?>
    
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
<a name="list" class="name">&nbsp;</a>
<h2><?php echo $h1;?></h2>
<?php
if((!empty($filter)) AND ($filter <> 'off')){
    $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
}



/*********************************
*
*	Começa a listagem
*
*********************************/
?>
<div class="listagem">
<div id="mod_imagens">
	
	
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">

<div class="painel_de_controle">
	<?php
	// VIEWMODE
	$thumbsClass = '';
	$listClass = '';
	if( $viewMode == 'thumbs' ) $thumbsClass = 'pressed';
	if( $viewMode == 'list' ) 	$listClass = 'pressed';
	?>
	<a href="javascript: void(0)" class="change_viewmode" name="thumbs">
	<span class="viewmode viewmode_thumbs viewmode_thumbs_<?php echo $austNode?> <?php echo $thumbsClass ?>">
	</span>
	</a>
	<a href="javascript: void(0)" class="change_viewmode" name="list">
	<span class="viewmode viewmode_list viewmode_list_<?php echo $austNode?> <?php echo $listClass ?>">
	</span>
	</a>
	
	<?php
	/*
	 * Pode excluir conteúdo?
	 */
	if( $permissoes->canDelete($austNode) ){
	    ?>
	
		<input type="submit" name="deletar" value="Deletar selecionados" />
	    <?php
	}
	?>
</div>

	<div id="listing_table">
	<?php
	
	// sets GETS
	
	if( empty($viewMode) OR $viewMode == 'list' )
		include($modulo->getIncludeFolder().'/view/mod/listing_list_view.php');
	else if( $viewMode == 'thumbs' )
		include($modulo->getIncludeFolder().'/view/mod/listing_thumbs_view.php');
		
		
	?>
	</div>

</form>
<?php
/*
 * PAGINAÇÃO
 * mostra painel de navegação para paginação
 */

    $total_registros = $modulo->totalRows;
	$page = $modulo->page();

    $total_paginas = $total_registros/$modulo->limit;

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
            if ($x==$page) {
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

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
</div>
</div>