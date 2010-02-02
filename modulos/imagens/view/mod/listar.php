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
$h1 = 'Listando conteúdo: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
$nome_modulo = $this->aust->LeModuloDaEstrutura($_GET['aust_node']);

$sql = "SELECT
            id,nome
        FROM
            ".Aust::$austTable."
        WHERE
            id='".$_GET['aust_node']."'";

            
$query = $this->aust->conexao->query($sql);

$cat = $query[0]['nome'];
?>
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
<h2><?php echo $h1;?></h2>
<p>Abaixo você encontra a listagem dos últimos itens desta categoria.</p>
<?php
if((!empty($filter)) AND ($filter <> 'off')){
    $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
}
	
$categorias = $this->aust->LeCategoriasFilhas('',$_GET['aust_node']);
$categorias[$_GET['aust_node']] = 'Estrutura';

// itens de paginação
$pagina = (empty($_GET['pagina'])) ? $pagina = 1 : $pagina = $_GET['pagina'];
$num_por_pagina = '10';//($config->LeOpcao($nome_modulo.'_paginacao')) ? $config->LeOpcao($nome_modulo.'_paginacao') : '10';
//echo $num_por_pagina;
// carrega o sql para listagem
$sql = $modulo->SQLParaListagem($categorias, $pagina, $num_por_pagina);
		//echo '<br><br>'.$sql .'<br>';


$query = $modulo->conexao->query($sql);


/*********************************
*
*	Começa a listagem
*
*********************************/
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">
<a name="list">&nbsp;</a>
<div class="painel_de_controle"><input type="submit" name="deletar" value="Deletar selecionados" />
</div>
<table cellspacing="0" cellpadding="10" class="listagem">
    <tr class="titulo">
        
        <?php for($i=0; $i< count($modulo->config['contentHeader']['campos']); $i++) { ?>
                <td class="<? echo $modulo->config['contentHeader']['campos'][$i]; ?>">
                    <?php
                        echo $modulo->config['contentHeader']['camposNome'][$i];
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
                for($i=0; $i< count($modulo->config['contentHeader']['campos']); $i++) { ?>
                    <td>
                        <?php
                        if($i == 1){
                            echo '<a href="adm_main.php?section='.$_GET['section'].'&action=editar&aust_node='.$_GET['aust_node'].'&w='.$dados["id"].'">';
                            echo $dados[$modulo->config['contentHeader']['campos'][$i]];
                            echo '</a>';
                        } else {
                            echo $dados[$modulo->config['contentHeader']['campos'][$i]];
                        }
                        ?>
                    </td>
            <?php } ?>
            <td align="center">
                <input type='checkbox' name='itens[]' value='<?php echo $dados['id'];?>'>
                <!-- <a href="adm_main.php?section=<?php echo $_GET['section']?>&action=see_info&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/lupa.jpg" alt="Ver Informações" border="0" /></a> -->
            <!--
                <a href="adm_main.php?section=<?php echo $_GET['section']?>&action=edit_form&aust_node=<?php echo $aust_node;?>&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/edit.jpg" alt="Editar" border="0" /></a>
                <?php
                if($escala == "administrador"
                OR $escala == "moderador"
                OR $escala == "webmaster"
                OR $_SESSION["loginid"] == $dados['autorid']){

                    if((!empty($filter)) AND ($filter <> 'off')){
                        $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
                    }
                    ?>
                    <a href="adm_main.php?section=<?php echo $_GET['section']?>&action=<?php echo $action;?>&block=delete&aust_node=<?php echo $aust_node;?>&w=<?php echo $dados["id"]; ?><?php echo $addurl;?>" style="text-decoration: none;"><img src="img/layoutv1/delete.jpg" alt="Deletar" border="0" /></a>
                    <?php
                }
                ?>
                <?php
                // Verifica se tipo conteúdo atual está configurado para usar galeria de fotos
                if(in_array($cat, $aust_conf['where_gallery'])){ ?>
                    <a href="adm_main.php?section=<?php echo $_GET['section']?>&action=photo_content_manage&w=<?php echo $dados["id"]; ?>#add" style="text-decoration: none;"><img src="img/layoutv1/fotos.jpg" alt="Adicionar fotos a este conteúdo" border="0" /></a>
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
    $total_registros = $modulo->conexao->count($sql);

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
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
</div>