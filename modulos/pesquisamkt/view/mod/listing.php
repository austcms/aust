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
<div class="listagem">
<?php
$h1 = 'Listando conteúdo: '.Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node']);
$nome_modulo = Aust::getInstance()->LeModuloDaEstrutura($_GET['aust_node']);
$sql = "SELECT
            id,nome
        FROM
            ".Aust::$austTable."
        WHERE
            id='".$_GET['aust_node']."'";

            
$query = $this->connection->query($sql);

$cat = $query[0]['nome'];
?>
<h2><?php echo $h1;?></h2>
<p>Abaixo você encontra a listagem dos últimos textos desta categoria.</p>
<?php
if((!empty($filter)) AND ($filter <> 'off')){
	$addurl = "&filter=$filter&filterw=" . urlencode($filterw);
}
	
$categorias = Aust::getInstance()->LeCategoriasFilhas('',$_GET['aust_node']);
$categorias[$_GET['aust_node']] = 'Estrutura';

// itens de paginação
$pagina = (empty($_GET['pagina'])) ? $pagina = 1 : $pagina = $_GET['pagina'];
$num_por_pagina = '10';//(Config::getInstance()->LeOpcao($nome_modulo.'_paginacao')) ? Config::getInstance()->LeOpcao($nome_modulo.'_paginacao') : '10';
//echo $num_por_pagina;
// carrega o sql para listagem
$sql = $module->SQLParaListagem($categorias, $pagina, $num_por_pagina);
		//echo '<br><br>'.$sql .'<br>';


$query = $module->connection->query($sql);
$query = $module->loadFirstQuestions($query);


/*********************************
*
*	Começa a listagem
*
*********************************/
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">
<a name="list">&nbsp;</a>
<?php

/*
 * Pode excluir conteúdo?
 */
if( StructurePermissions::getInstance()->canDelete($austNode) ){
    ?>
    <div class="painel_de_controle"><input type="submit" class="js_confirm" name="deletar" value="Deletar selecionados" />
    </div>
    <?php
}
?>
<table cellspacing="0" cellpadding="10" class="listagem">
    <tr class="titulo">
        
        <?php for($i=0; $i< count($module->config['contentHeader']['campos']); $i++) { ?>
                <td class="<? echo $module->config['contentHeader']['campos'][$i]; ?>">
                    <?php
                        echo $module->config['contentHeader']['camposNome'][$i];
                    ?>
                </td>
        <?php } ?>
        <td bgcolor="#333333" width="80" align="center">
            Op&ccedil;&otilde;es
        </td>
    </tr>
<?php
if(count($query) == 0){
    ?>
    <tr class="conteudo">
        <td colspan="<?php echo $i+1;?>">
            <strong>Nenhum registro encontrado.</strong>
        </td>
    </tr>
    <?php
} else {
    foreach($query as $dados){
        ?>
        <tr class="conteudo">
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
                            $titulo = $dados[$module->config['contentHeader']['campos'][$i]];

							if( $module->getStructureConfig('has_no_title') ){
                                echo $dados['question']['text'];
							} else {
	                            if( empty($titulo) )
	                                echo "<em>Sem título</em>";
	                            else
	                                echo $titulo;
							}
							
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

    $sql = $module->SQLParaListagem($categorias);
    $total_registros = $module->connection->count($sql);

    $total_paginas = $total_registros/$num_por_pagina;

    $prev = $pagina - 1;
    $next = $pagina + 1;
    // se página maior que 1 (um), então temos link para a página anterior
    if ($pagina > 1) {
        $prev_link = ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$prev.'">Anterior</a>';
    } else { // senão não há link para a página anterior
        $prev_link = "Anterior";
    }
    // se número total de páginas for maior que a página corrente,
    // então temos link para a próxima página
    if ($total_paginas > $pagina) {
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
            if ($x==$pagina) {
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