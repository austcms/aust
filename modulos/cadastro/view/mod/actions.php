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
        $itens = $_POST['itens'];
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
                    id, url, arquivo_nome
                FROM
                    arquivos
                WHERE
                    {$where}";
        //$mysql = mysql_query($sql);
        //$dados = mysql_fetch_array($mysql);

        $sql = "DELETE FROM
                    ".$module->LeTabelaDaEstrutura($_GET['aust_node'])."
                WHERE
                    $where
                    ";
        if($module->connection->exec($sql)){
            $resultado = TRUE;
        } else {
            $resultado = FALSE;
        }

		$sql = "SELECT *
				FROM cadastros_conf
				WHERE 
					tipo='campo' AND
					(
						especie='relacional_umparamuitos' OR
						especie='relational_onetomany'
					) AND
					categorias_id = '".$module->austNode."'
				";

		$tables = $this->connection->query($sql);
		
		foreach( $tables as $table ){
			$sqlDelete = "
						DELETE FROM ".$table['referencia']."
						WHERE ".$table['ref_parent_field']." IN ('".implode("','", $itens)."')
						";

        	$this->connection->exec($sqlDelete);
		}

        if($resultado){
            $status['classe'] = 'sucesso';
            $status['mensagem'] = '<strong>Sucesso: </strong> Os dados foram excluídos com sucesso.';
        } else {
            $status['classe'] = 'insucesso';
            $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao excluir os dados.';
        }
        EscreveBoxMensagem($status);

/*
 * APROVAR usuário
 */
} elseif(!empty($_POST['aprovar']) and !empty($_POST['itens'])){
    /*
     * Identificar tabela que deve ser excluida
     */
        $itens = $_POST['itens'];
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
                    approved='1'
                WHERE
                    $where
                    ";
        //echo $sql;
        if($module->connection->exec($sql)){
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

?>