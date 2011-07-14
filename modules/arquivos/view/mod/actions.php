<?php

/*
 * DELETAR, APROVAR
 */

// para deletar
if(!empty($_POST['deletar']) and !empty($_POST['itens'])){
    /*
     * Identificar tabela que deve ser excluida
     */

        $itens = $_POST["itens"];
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
                    ".$module->getMainTable()."
                WHERE
                    {$where}";
        $mysql = $module->connection->query($sql);
		if( !empty($mysql) )
        	$dados = $mysql[0];
		

        // se conseguir excluir o arquivo fisicamente, então exclui dados do DB
        try{

            if( !is_file($dados['systemurl'])
                OR unlink($dados['systemurl'])
                OR die('Erro ao deletar arquivo, pois ele provavelmente não existe mais. Entre em contato com o administrador.</p>'))
            {

                $sql = "DELETE FROM
                            ".$module->getMainTable()."
                        WHERE
                            $where
                            ";
                if($module->connection->exec($sql)){
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
/*
 * APROVAR usuário
 */
}

?>