<?php
/**
 * Responsável pela leitura de dados do DB e listagem.
 *
 * @package [Nome do pacote de Classes, ou do sistema]
 * @category [Categoria a que o arquivo pertence]
 * @name [Apelido para o arquivo]
 * @author [nome do autor] <[e-mail do autor]>
 * @copyright [Informações de Direitos de Cópia]
 * @license [link da licença] [Nome da licença]
 * @link [link de onde pode ser encontrado esse arquivo]
 * @version [Versão atual do arquivo]
 * @since [Arquivo existe desde: Data ou Versao]
 */


$h1 = 'Listando conteúdo: '.$aust->leNomeDaEstrutura($_GET[aust_node]);
$specsection['list_content_description'] = 
                                        '<p>
                                            A seguir, o conteúdo do site. Ao lado, há opções que podem ser tomadas quanto
                                            ao respectivo conteúdo.
                                        </p>';


    $sql = "SELECT
                id,nome
            FROM
                $aust_table
            WHERE
                id='$aust_node'";


    $mysql = mysql_query($sql);
    $dados = mysql_fetch_array($mysql);
    $cat = $dados[nome];
?>
<h2><?php echo $h1;?></h2>
<p>
    <a href="adm_main.php?section=<?php echo $section;?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
<?php
    if((!empty($filter)) AND ($filter <> 'off')){
        $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
    }
?>

<?php echo $specsection[list_content_description];?>

<?php
if($block == "block"){
    $sql = "UPDATE $section
            SET
                bloqueado='bloqueado'
            WHERE
                id='$w'
            ";
    if (mysql_query($sql)){
    ?>
        <div style="width: 680px; display: table;">
            <div style="background: red; padding: 15px; text-align: center;">
                <p style="color: white; margin: 0px;">
                    O conte&uacute;do foi bloqueado com sucesso! Entretanto, ele n&atilde;o foi deletado.
                </p>

                <?php
                if($escala == "administrador"
                OR $escala == "moderador"
                OR $escala == "webmaster"){
                ?>
                    <p style="color: white; margin: 0px;">
                        <a href="adm_main.php?section=<?php echo $section;?>&action=<?php echo $action;?>&block=delete&w=<?php echo $w; ?><?php echo $addurl;?>" style="text-decoration: underline; color: white;">-> Clique aqui para apagar o conte&uacute;do definitivamente <- </a>
                    </p>
                <?php } ?>
            </div>
        </div>
    <?php
    } else {
        echo '<p style="color: red;">Ocorreu um erro desconhecido ao editar as informações do usuário, tente novamente.</p>';
    }
} else if($block == "unblock"){
    $sql = "UPDATE $section
            SET
                bloqueado='livre',
                publico='sim'
            WHERE
                id='$w'
            ";
    if (mysql_query($sql)){
    ?>
        <div style="width: 680px; display: table;">
            <div style="background: green; padding: 15px; text-align: center;">
                <p style="color: white; margin: 0px;">
                    O conte&uacute;do foi desbloqueado com sucesso! Agora ele aparecer&aacute; no site.
                </p>
            </div>
        </div>
    <?php
    } else {
        echo '<p style="color: red;">Ocorreu um erro desconhecido ao editar as informações do usuário, tente novamente.</p>';
    }
} else if($block == "delete"){
    if(empty($confirm)){
    ?>
        <div style="width: 680px; display: table;">
            <div style="background: yellow; padding: 15px; text-align: center;">
                <p style="color: black; margin: 0px;">
                    <strong>
                    Tem certeza que deseja apagar o item selecionado?
                    </strong>
                    <br />
                    <a href="adm_main.php?section=<?php echo $section;?>&action=<?php echo $action;?>&block=delete&aust_node=<?php echo $aust_node;?>&w=<?php echo $w;?>&confirm=delete<?php echo $addurl;?>">Sim</a> -
                    <a href="adm_main.php?section=<?php echo $section;?>&action=<?php echo $action;?>&aust_node=<?php echo $aust_node;?><?php echo $addurl;?>">N&atilde;o</a>
                </p>
            </div>
        </div>
    <?php
    } else if($confirm == "delete"){

        $verifysql = "
                SELECT
                    id,filetype,filename,url,autorid
                FROM
                    $section
                WHERE
                    id='$w'";
        $verifymysql = mysql_query($verifysql);
        $dados = mysql_fetch_array($verifymysql);

        if($escala == "administrador"
        OR $escala == "moderador"
        OR $escala == "webmaster"
        OR $_SESSION["loginid"] == $dados[autorid]){
            $sql = "DELETE FROM $section
                    WHERE
                        id='$w'
                    ";



            if (mysql_query($sql)){
                if($dados["filetype"] <> ''){
                    chdir('../'.dirname($dados[url]).'/');
                    $do = unlink($dados["filename"]);
                    if(!$do)
                        echo '<p style="color: green">Erro ao deletar arquivo.</p>';
                    else
                        echo '<p>Arquivo deletado com sucesso.</p>';
                }
                ?>
                <div style="width: 680px; display: table;">
                    <div style="background: black; padding: 15px; text-align: center;">
                        <p style="color: white; margin: 0px;">
                            <strong>
                            O conte&uacute;do foi apagado definitivamente!
                            </strong>
                        </p>
                    </div>
                </div>
                <?php

            } else {
                echo '<p style="color: red;">Ocorreu um erro desconhecido ao deletar as informações, tente novamente.</p>';
            }
        }
    }
}

    $categorias = $aust->LeCategoriasFilhas('',$_GET[aust_node]);
    $categorias[$_GET[aust_node]] = 'Estrutura';
    $sql = $modulo->SQLParaListagem($categorias);
//		echo '<br><br>'.$sql .'<br>';


    $mysql = mysql_query($sql);
?>
    <a name="list">&nbsp;</a>
    <table width="680" cellspacing="0" cellpadding="10" class="listagem">
        <tr class="titulo">

            <?php for($i=0; $i< count($content_header[campos]); $i++) { ?>
                    <td bgcolor="#333333" class="<?php echo $content_header[campos][$i]; ?>">
                        <?php
                            echo $content_header[campos_nome][$i];
                        ?>
                    </td>
            <?php } ?>
            <td bgcolor="#333333" width="80">
                Op&ccedil;&otilde;es
            </td>
        </tr>
<?php
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
                            echo '<a href="adm_main.php?section='.$section.'&action=editar&aust_node='.$aust_node.'&w='.$dados["id"].'">';
                            echo $dados[$content_header[campos][$i]];
                            echo '</a>';
                        } else {
                            echo $dados[$content_header[campos][$i]];
                        }
                        ?>
                    </td>
            <?php } ?>
            <td>
                <a href="adm_main.php?section=<?php echo $section;?>&action=edit_form&aust_node=<?php echo $aust_node;?>&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="<?php echo IMG_DIR?>layoutv1/edit.jpg" alt="Editar" border="0" /></a>
                <?php
                if($escala == "administrador"
                OR $escala == "moderador"
                OR $escala == "webmaster"
                OR $_SESSION["loginid"] == $dados['autorid']){

                    if((!empty($filter)) AND ($filter <> 'off')){
                        $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
                    }
                    ?>
                    <a href="adm_main.php?section=<?php echo $section;?>&action=<?php echo $action;?>&block=delete&aust_node=<?php echo $aust_node;?>&w=<?php echo $dados["id"]; ?><?php echo $addurl;?>" style="text-decoration: none;"><img src="<?php echo IMG_DIR?>layoutv1/delete.jpg" alt="Deletar" border="0" /></a>
                    <?php
                }
                ?>
                <?php
                // Verifica se tipo conteúdo atual está configurado para usar galeria de fotos
                if(in_array($cat, $aust_conf['where_gallery'])){ ?>
                    <a href="adm_main.php?section=<?php echo $section;?>&action=photo_content_manage&w=<?php echo $dados["id"]; ?>#add" style="text-decoration: none;"><img src="<?php echo IMG_DIR?>layoutv1/fotos.jpg" alt="Adicionar fotos a este conteúdo" border="0" /></a>
                <?php } ?>
            </td>
        </tr>
    <?php
    }
    echo '</table>';
?>

<p style="margin-top: 15px;">
    <a href="adm_main.php?section=<?php echo $section;?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>