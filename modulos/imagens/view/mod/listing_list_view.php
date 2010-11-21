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
                    <input type='checkbox' name='itens[]' value='<?php echo $dados['id'];?>' />
                    <?php
                }
                ?>
            <!--
                <a href="adm_main.php?section=<?php echo $_GET['section']?>&action=edit_form&aust_node=<?php echo $austNode;?>&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/edit.jpg" alt="Editar" border="0" /></a>
                <?php
                if($escala == "administrador"
                OR $escala == "moderador"
                OR $escala == "webmaster"
                OR $_SESSION["loginid"] == $dados['autorid']){

                    if((!empty($filter)) AND ($filter <> 'off')){
                        $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
                    }
                    ?>
                    <a href="adm_main.php?section=<?php echo $_GET['section']?>&action=<?php echo $action;?>&block=delete&aust_node=<?php echo $austNode;?>&w=<?php echo $dados["id"]; ?><?php echo $addurl;?>" style="text-decoration: none;"><img src="img/layoutv1/delete.jpg" alt="Deletar" border="0" /></a>
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