<?php
/*
 * LISTAR USUÁRIOS CADASTRADOS
 *
 * Este arquivo contém tudo necessário para listagem geral de usuários cadastrados
 */

// configuração: ajusta variáveis
$tabela = $modulo->LeTabelaDeDados($_GET['aust_node']);
?>

<p><a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a></p>
<h1>Listando conteúdo: <?php echo $aust->leNomeDaEstrutura($_GET['aust_node']);?></h1>
<p>A seguir você vê a lista de registros sob o cadastro "<?php echo $aust->leNomeDaEstrutura($_GET['aust_node'])?>".</p>

<?
/*
 * Verifica se há a necessidade de aprovação de cadastro e se há alguém necessitando aprovação
 */
if($precisa_aprovacao['valor'] == '1'){
    $sql = "SELECT id FROM ".$tabela." WHERE aprovado=0";
    $result = mysql_query($sql);
    if(mysql_num_rows($result) == 1){
        echo '<p>Há, atualmente, <strong>1</strong> usuário precisando de aprovação.</p>';
    } else if(mysql_num_rows($result) > 1){
        echo '<p>Há <strong>'.mysql_num_rows($result).'</strong> usuário precisando de aprovação.</p>';
    }
}
?>

<?php

$categorias = $aust->LeCategoriasFilhas('',$_GET[aust_node]);
$categorias[$_GET[aust_node]] = 'Estrutura';
$param = Array(
                /* 'categorias' => $categorias, */
                'metodo' => 'listar',
                '' => ''
);
$sql = $modulo->SQLParaListagem($param);
//echo '<br><br>'.$sql .'<br>';
$mysql = mysql_query($sql);
$fields = mysql_num_fields($mysql);

//print_r($precisa_aprovacao);
/*
 * Começa a listagem
 */

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">
    <a name="list">&nbsp;</a>

    <? // Painel de controle ?>
    <div class="painel_de_controle">Selecionados:
        <?
        // se este cadastro precisa de aprovação, mostra botão para aprovar usuário
        if($precisa_aprovacao['valor'] == '1'){ ?>
            <input type="submit" name="aprovar" value="Aprovar" />
        <? } ?>
        <input type="submit" name="deletar" value="Deletar" />
    </div>
<table width="680" cellspacing="0" cellpadding="10" class="listagem">
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
    if(mysql_num_rows($mysql) > 0){
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
                            echo $dados[$content_header['campos'][$i]];
                            echo '</a>';
                        } else {
                            echo $dados[$content_header['campos'][$i]];
                        }
                        ?>
                    </td>
                <?php } ?>
                <td align="center">
                    <? if($dados['classe'] <> 'padrão'){ ?><input type='checkbox' name='itens[]' value='<?php echo $dados['id'];?>'>
                    <? } ?>
                    <!-- <a href="adm_main.php?section=<?php echo $_GET['section']?>&action=see_info&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/lupa.jpg" alt="Ver Informações" border="0" /></a> -->
                <!--
                    <a href="adm_main.php?section=<?php echo $_GET['section']?>&action=edit_form&aust_node=<?php echo $aust_node;?>&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/edit.jpg" alt="Editar" border="0" /></a>
                    <?php
                    if($escala == "administrador"
                    OR $escala == "moderador"
                    OR $escala == "webmaster"
                    OR $_SESSION["loginid"] == $dados[autorid]){

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
        } // FIM DO WHILE
    } else {
        ?>
        <tr>
            <td colspan="<?php echo count($content_header)+1;?>">
            <strong>Não há arquivos cadastrados.</strong>
            </td>
        </tr>
        <?
    }
    ?>
</table>
</form>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>