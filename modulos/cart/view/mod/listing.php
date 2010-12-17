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
<div class="listagem cart">
<h2>
    Listando contéudo: <?php echo $h1;?>
</h2>
<p>
    Abaixo você encontra a listagem dos últimos textos desta categoria.
</p>
<?php


/*********************************
*
*	Começa a listagem
*
*********************************/
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">
<a name="list"></a>
<?php
/*
 * Pode excluir conteúdo?
 */
/*
if( $permissoes->canDelete($austNode) ){
    ?>
    <div class="painel_de_controle"><input type="submit" class="js_confirm" name="deletar" value="Deletar selecionados" />
    </div>
    <?php
}
*/
?>
<br clear="all" />

<?php
foreach( $query as $dados ){
	
	$statusClass = 'ready';
	if( $dados['pending'] == '1' ){
		$statusClass = 'pending';
	}
	if( $dados['paid'] == '0' ){
		$statusClass = 'not_paid';
	}
    ?>
	<div class="transaction <?php echo $statusClass ?>">
		<div class="containner">

			<div class="status">
				<?php
				if( $dados['paid'] == '0' ){
					?>
					Não-pago
					<?php
				} else if( $dados['pending'] == '1' ){
					?>
					Pendente
					<?php
				} else {
					?>
					Completa
					<?php
				}
				?>
				
			</div>
			<div class="number">
			<?php echo $dados['transaction_nr']?>
			</div>
			<?php if( !empty($dados['created_on']) ){ ?>
				<div class="date scheduled_to">
				<div class="label">Realizada em: </div>
				<div class="value"><?php echo $dados['created_on']?></div>
				</div>
			<?php } ?>
			<?php if( !empty($dados['scheduled_on']) ){ ?>
				<div class="date scheduled_to">
				<div class="label">Agendado para: </div>
				<div class="value"><?php echo $dados['scheduled_on']?></div>
				</div>
			<?php } ?>
			<div class="actions">
			<a href="adm_main.php?section=conteudo&action=edit&aust_node=<?php echo $austNode?>&w=<?php echo $dados['id']?>">
				Ver detalhes
			</a>
			</div>
			<div>
			<?php
			/*
            if( $permissoes->canDelete($austNode) ){
                ?>
                <input type='checkbox' name='itens[]' value='<?php echo $dados['id'];?>'>
                <?php
            }
			*/
            ?>
			</div>
            
		
		</div>
	</div>
	<?php
}
?>

</form>
<br clear="all" />
<?php
/*
 * PAGINAÇÃO
 * mostra painel de navegação para paginação
 */

    //$sql = $modulo->getSQLForListing($categorias);
    $total_registros = $modulo->totalRows;

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

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
</div>