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
        <form method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING'];?>&confirm=delete" name="repost">
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

        $sql = "SELECT
                    *
                FROM
                    ".$modulo->getMainTable()."
                WHERE
                    {$where}";
        $mysql = $modulo->connection->query($sql);
        $dados = $mysql[0];

        // se conseguir excluir o arquivo fisicamente, então exclui dados do DB
        try{

            if( !is_file($dados['systemurl'])
                OR unlink($dados['systemurl'])
                OR die('Erro ao deletar arquivo, pois ele provavelmente não existe mais. Entre em contato com o administrador.</p>'))
            {

                $sql = "DELETE FROM
                            ".$modulo->getMainTable()."
                        WHERE
                            $where
                            ";
                if($modulo->connection->exec($sql)){
                    $resultado = TRUE;
                } else {
                    $resultado = FALSE;
                }

                if($resultado){
                    $status['classe'] = 'sucesso';
                    $status['mensagem'] = '<strong>Sucesso: </strong> Os dados foram excluídos com sucesso.';
                } else {
                    $status['classe'] = 'insucesso';
                    $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao excluir os dados. '.
                                          'Verifique se o arquivo já não foi apagado.';
                }
                EscreveBoxMensagem($status);
            } else {
                $status['classe'] = 'insucesso';
                $status['mensagem'] = '<strong>Erro: </strong> Não foi possível excluir o arquivo.';
                EscreveBoxMensagem($status);
            }
        } catch (Exception $e) {
            echo 'exceção: ', $e->getMessage(), "<br>";
        }

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
        <form method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING'];?>&confirm=aprovar" name="repost">
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
                    ".$modulo->LeTabelaDaEstrutura($_GET['aust_node'])."
                SET
                    approved='1'
                WHERE
                    $where
                    ";
        //echo $sql;
        if($modulo->connection->exec($sql)){
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