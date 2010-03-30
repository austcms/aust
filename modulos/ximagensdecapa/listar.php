<div class="listagem">
<?php
$h1 = 'Listando conteúdo: '.$aust->leNomeDaEstrutura($_GET[aust_node]);
$nome_modulo = $aust->LeModuloDaEstrutura($_GET[aust_node]);
$sql = "SELECT
			id,nome
		FROM
			$aust_table
		WHERE
			id='$austNode'";
			
			
$mysql = mysql_query($sql);
$dados = mysql_fetch_array($mysql);
$cat = $dados[nome];
?>
<p>
	<a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
<h2><?=$h1;?></h2>
<p>Abaixo você encontra a listagem dos últimos textos desta categoria.</p>
<?php
if((!empty($filter)) AND ($filter <> 'off')){
	$addurl = "&filter=$filter&filterw=" . urlencode($filterw);
}
	
$categorias = $aust->LeCategoriasFilhas('',$_GET[aust_node]);
$categorias[$_GET[aust_node]] = 'Estrutura';

// itens de paginação
$pagina = (empty($_GET['pagina'])) ? $pagina = 1 : $pagina = $_GET['pagina'];
$num_por_pagina = ($config->LeOpcao($nome_modulo.'_paginacao')) ? $config->LeOpcao($nome_modulo.'_paginacao') : '10';
//echo $num_por_pagina;
// carrega o sql para listagem
$sql = $modulo->SQLParaListagem($categorias, $pagina, $num_por_pagina);
		//echo '<br><br>'.$sql .'<br>';


$mysql = mysql_query($sql);


/*********************************
*
*	Começa a listagem
*
*********************************/
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>?section=<?=$_GET[section];?>&action=actions&aust_node=<?=$_GET[aust_node];?>">
<a name="list">&nbsp;</a>
<div class="painel_de_controle"><input type="submit" name="deletar" value="Deletar selecionados" />
</div>
<table cellspacing="0" cellpadding="10" class="listagem">
    <tr class="titulo">
        
        <?php for($i=0; $i< count($content_header[campos]); $i++) { ?>
                <td class="<? echo $content_header[campos][$i]; ?>">
                    <?php
                        echo $content_header[campos_nome][$i];
                    ?>
                </td>
        <?php } ?>
        <td bgcolor="#333333" width="80" align="center">
            Op&ccedil;&otilde;es
        </td>
    </tr>
<?
if(mysql_num_rows($mysql) == 0){
    ?>
    <tr class="conteudo">
        <td colspan="<?=$i+1;?>">
            <strong>Nenhum registro encontrado.</strong>
        </td>
    </tr>
    <?
} else {
    while($dados = mysql_fetch_array($mysql)){
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
                for($i=0; $i< count($content_header[campos]); $i++) { ?>
                    <td>
                        <?php
                        if($i == 1){
                            echo '<a href="adm_main.php?section='.$_GET['section'].'&action=editar&aust_node='.$_GET['aust_node'].'&w='.$dados["id"].'">';
                            echo $dados[$content_header[campos][$i]];
                            echo '</a>';
                        } else {
                            echo $dados[$content_header[campos][$i]];
                        }
                        ?>
                    </td>
            <?php } ?>
            <td align="center">
                <input type='checkbox' name='itens[]' value='<?=$dados[id];?>'>
                <!-- <a href="adm_main.php?section=<?=$_GET['section']?>&action=see_info&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/lupa.jpg" alt="Ver Informações" border="0" /></a> -->
            <!--
                <a href="adm_main.php?section=<?=$_GET['section']?>&action=edit_form&aust_node=<?=$austNode;?>&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/edit.jpg" alt="Editar" border="0" /></a>
                <?php
                if($escala == "administrador"
                OR $escala == "moderador"
                OR $escala == "webmaster"
                OR $_SESSION["loginid"] == $dados[autorid]){

                    if((!empty($filter)) AND ($filter <> 'off')){
                        $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
                    }
                    ?>
                    <a href="adm_main.php?section=<?=$_GET['section']?>&action=<?=$action;?>&block=delete&aust_node=<?=$austNode;?>&w=<?php echo $dados["id"]; ?><?php echo $addurl;?>" style="text-decoration: none;"><img src="img/layoutv1/delete.jpg" alt="Deletar" border="0" /></a>
                    <?php
                }
                ?>
                <?php
                // Verifica se tipo conteúdo atual está configurado para usar galeria de fotos
                if(in_array($cat, $aust_conf['where_gallery'])){ ?>
                    <a href="adm_main.php?section=<?=$_GET['section']?>&action=photo_content_manage&w=<?php echo $dados["id"]; ?>#add" style="text-decoration: none;"><img src="img/layoutv1/fotos.jpg" alt="Adicionar fotos a este conteúdo" border="0" /></a>
                <?php } ?>
               -->
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

    $sql = $modulo->SQLParaListagem($categorias);
    $total_registros = mysql_num_rows(mysql_query($sql));

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
	<a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
</div>