<?php

/*
 * GALERIA DE IMAGENS
 *
 * EMBED OWN FORM
 */

/**
 * EDITE!!!!!!!
 *
 * Nome do action Hidden deste form
 */
    $thisFormGravarAction = 'insertimagem';

// faz insert dos dados caso tenham sido enviados
if( !empty($_POST['action']) AND ($_POST['action'] == $thisFormGravarAction) ){
    $result = $modulo->insertImagem($_POST, $_FILES, $_GET);

    if($result['query']){
        $status['mensagem'] = 'Imagem inserida com sucesso!';
        $status['classe'] = 'sucesso';
        EscreveBoxMensagem($status);
    } else {
        if( !empty($result['status']) AND $result['status'] == 'max_size'){
            $status['mensagem'] = 'O arquivo é muito grande.';
        } else {
            $status['mensagem'] = 'Ocorreu um erro ao inserir imagem .';
        }
        $status['classe'] = 'insucesso';
        EscreveBoxMensagem($status);
    }
}


?>
<a name="galeriadeimagens"></a>
<?php
// @todo - quando submit, se já houver apagado uma imagem, fica aparecendo a mensagem de imagem deletada
?>
<form method="post" action="#galeriadeimagens" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $thisFormGravarAction; ?>" />
    <input type="hidden" name="w" value="<?php echo $_GET['w'];?>" />
    <input type="hidden" name="aust_node" value="<?php echo $_GET['aust_node'];?>" />
    <input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmclasse" value="galeria" />
    <input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
    <p>Use o formulário abaixo para inserir uma nova foto na <strong>galeria de imagens</strong>.</p>

    <?php
    if(!empty($_GET['deletegaleriaimagens'])){
        $sql = "DELETE FROM galeriaimagens WHERE id='".$_GET['deletegaleriaimagens']."'";
        if($modulo->connection->exec($sql)){
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

    <table width="99%" border="0">
    <col width="200">
    <col>
    <tr>
        <td>
            Selecione uma foto:
        </td>
        <td>
            <input type="file" name="frmarquivo" />
        </td>
    </tr>
    <tr>
        <td>
            Descrição:
        </td>
        <td>
            <input type="text" name="frmdescricao" />
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
    <table width="100%" border="0">
        <?php
        $modulo->criaThumbsTable(
                                    array(
                                        'conditions' => array(
                                                        'ref' => $_GET['w'],
                                                        'categoria' => $aust->LeCategoriasFilhas('', $_GET['aust_node'])
                                        ),
                                        'columns' => '3',
                                        'script' => IMAGE_VIEWER_DIR.'visualiza_foto.php?thumbs=yes&ysize=60&table=galeriaimagens&myid=',
                                        'inline' => 'style="text-align: center; padding-bottom: 15px;"',
                                        'options' => '<a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&w='.$_GET['w'].'&deletegaleriaimagens=&%id#galeriadeimagens">Apagar</a>',
                                        'empty' => 'Não há imagens cadastradas.'
                                    )
                                );
        ?>

    </table>
</div>


