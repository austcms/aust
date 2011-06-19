<?php

/*
 * DELETAR, APROVAR
 */

// para deletar
if(!empty($_POST['deletar']) and !empty($_POST['itens'])){
    /*
     * Identificar tabela que deve ser excluida
     */

    // se não estiver confirmada a exclusão
    if(empty($_GET['confirm'])){
    ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?<?php echo $_SERVER['QUERY_STRING'];?>&confirm=delete" name="repost">
        <input type="hidden" name="deletar" value="deletar" />
        <?php
        $itens = $_POST['itens'];
        foreach($itens as $key=>$valor){
            echo '<input type="hidden" name="itens[]" value="'.$valor.'" />';
        }
        $status['classe'] = 'pergunta';
        $status['mensagem'] = '<strong>
                Tem certeza que deseja apagar o(s) item(ns) selecionado(s)?
                </strong>
                <br />
                <a href="#" onclick="document.repost.submit(); return false">Sim</a> -
                <a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=listar">N&atilde;o</a>';
        EscreveBoxMensagem($status);
        ?>
        </form>
    <?
    // se estiver confirmada a exclusão
    } else if($_GET['confirm'] == "delete"){
        $itens = $_POST[itens];
        $c = 0;
        foreach($itens as $key=>$valor){
            if($c > 0){
                $where = $where." OR id='".$valor."'";
            } else {
                $where = "id='".$valor."'";
            }
            $c++;
        }


        $sql = "DELETE FROM
                    ".$module->LeTabelaDaEstrutura($_GET['aust_node'])."
                WHERE
                    $where
                    ";
        if(mysql_query($sql)){
            $resultado = TRUE;
        } else {
            $resultado = FALSE;
        }

        if($resultado){
            $status['classe'] = 'sucesso';
            $status['mensagem'] = '<strong>Sucesso: </strong> Os dados foram excluídos com sucesso.';
        } else {
            $status['classe'] = 'insucesso';
            $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao excluir os dados.';
        }
        EscreveBoxMensagem($status);
           
      
    }

/*
 * APROVAR usuário
 */
} elseif(!empty($_POST['aprovar']) and !empty($_POST['itens'])){
    /*
     * Identificar tabela que deve ser excluida
     */

    // se não estiver confirmada a exclusão
    if(empty($_GET['confirm'])){
    ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?<?php echo $_SERVER['QUERY_STRING'];?>&confirm=aprovar" name="repost">
        <input type="hidden" name="aprovar" value="aprovar" />
        <?php
        $itens = $_POST['itens'];
        foreach($itens as $key=>$valor){
            echo '<input type="hidden" name="itens[]" value="'.$valor.'" />';
        }
        $status['classe'] = 'pergunta';
        $status['mensagem'] = '<strong>
                Tem certeza que deseja executar a ação requerida?
                </strong>
                <br />
                <a href="#" onclick="document.repost.submit(); return false">Sim</a> -
                <a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=listar">N&atilde;o</a>';
        EscreveBoxMensagem($status);
        ?>
        </form>
    <?
    // se estiver confirmada a ação
    } else if($_GET['confirm'] == "aprovar"){
        $itens = $_POST[itens];
        $c = 0;
        foreach($itens as $key=>$valor){
            if($c > 0){
                $where = $where." OR id='".$valor."'";
            } else {
                $where = "id='".$valor."'";
            }
            $c++;
        }

        
        $sql = "UPDATE
                    ".$module->LeTabelaDaEstrutura($_GET['aust_node'])."
                SET
                    aprovado='1'
                WHERE
                    $where
                    ";
        if(mysql_query($sql)){
            $resultado = TRUE;
        } else {
            $resultado = FALSE;
        }

        if($resultado){
            $status['classe'] = 'sucesso';
            $status['mensagem'] = '<strong>Sucesso: </strong> Usuário(s) aprovado(s) com sucesso.';
        } else {
            $status['classe'] = 'insucesso';
            $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao aprovar usuário(s) os dados.';
        }
        EscreveBoxMensagem($status);
    }
}

?>