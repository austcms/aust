<?php

/*
 * GALERIA DE IMAGENS
 *
 * EMBED OWN FORM
 */

/**
 *  EDITE!!!!!!!
 *
 * Nome do action Hidden deste form
 */
    $thisFormGravarAction = 'insertimagemcapa';



// faz insert dos dados caso tenham sido enviados
if($_POST['action'] == $thisFormGravarAction){
    $result = $modulo->insertImagem($_POST, $_FILES, $_GET);
    //pr($result);
    if($result['query']){
        $status['mensagem'] = 'Imagem inserida com sucesso!';
        $status['classe'] = 'sucesso';
        EscreveBoxMensagem($status);
    } else {
        if($result['status'] == 'max_size'){
            $status['mensagem'] = 'O arquivo é muito grande.';
        } else {
            $status['mensagem'] = 'Ocorreu um erro ao inserir imagem .';
        }
        $status['classe'] = 'insucesso';
        EscreveBoxMensagem($status);
    }
}


?>
<a name="galeriadeimagenscapa"></a>
<?
// @todo - quando submit, se já houver apagado uma imagem, fica aparecendo a mensagem de imagem deletada
?>
<form method="post" action="#galeriadeimagenscapa" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $thisFormGravarAction ?>" />
    <input type="hidden" name="w" value="<?=$_GET['w'];?>" />
    <input type="hidden" name="frmcategoria" value="<?=$_GET['aust_node'];?>" />
    <input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmclasse" value="capa" />
    <input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
    <?php

    /*
     * EXCLUIR IMAGEM
     */

    // verifica se houve ordem de exclusão, mas que não seja inclusão de um form
    if(!empty($_GET['deletegaleriaimagenscapa']) AND empty($_POST['action'])){
        $sql = "DELETE FROM galeriaimagenscapa WHERE id='".$_GET['deletegaleriaimagenscapa']."'";
        if(mysql_query($sql)){
            $status['mensagem'] = 'Imagem excluída com sucesso!';
            $status['classe'] = 'sucesso';
            EscreveBoxMensagem($status);
        } else {
            $status['mensagem'] = 'Erro ao excluir imagem.';
            $status['classe'] = 'insucesso';
            EscreveBoxMensagem($status);
        }
    }
    ?>
    <p style="margin-bottom: 0">Insira um nova imagem de <strong>capa</strong>.</p>

    <table width="99%" border= "0">
    <col width="200">
    <col>
    <tr>
        <td>
            Selecione uma foto:
        </td>
        <td>
            <input type="file" name="capaarquivo" />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="submit" value="Enviar" />
        </td>
    </tr>
    </table>
    
</form>
<br />
<div>
    <strong>Imagens deste item</strong>
    <table width="100%" border="0" cellspacing="0">
        <?php
        $modulo->criaThumbsTable(
                                    array(
                                        'conditions' => array(
                                                        'ref' => $_GET['w'],
                                                        'categoria' => $aust->LeCategoriasFilhas('', $_GET['aust_node'])
                                        ),
                                        'columns' => '3',
                                        'script' => IMAGE_VIEWER_DIR.'visualiza_foto.php?thumbs=yes&ysize=60&table=galeriaimagenscapa&myid=',
                                        'inline' => 'style="text-align: center; padding-bottom: 15px;"',
                                        'options' => '<a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&w='.$_GET['w'].'&deletegaleriaimagenscapa=&%id#galeriadeimagenscapa">Apagar</a>',
                                        'empty' => '<em>Não há imagens cadastradas.</em>'
                                    )
                                );
        ?>

    </table>
</div>


