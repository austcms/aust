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
<?
if(count($query) > 0){
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
                        if( $permissoes->canEdit($_GET['aust_node']) )
                            echo '<a href="adm_main.php?section='.$_GET['section'].'&action=edit&aust_node='.$_GET['aust_node'].'&w='.$dados["id"].'">';
                        echo $dados[$modulo->config['contentHeader']['campos'][$i]];
                        if( $permissoes->canEdit($_GET['aust_node']) )
                            echo '</a>';
                    } else {
                        echo $dados[$modulo->config['contentHeader']['campos'][$i]];
                    }
                    ?>
                </td>
            <?php } ?>
            <td align="center">
                <?php
                if( $permissoes->canDelete($austNode) ){
                    ?>
                    <input type='checkbox' name='itens[]' value='<?php echo $dados['id'];?>'>
                    <?php
                }
                ?>
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