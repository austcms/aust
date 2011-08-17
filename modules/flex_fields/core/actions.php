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
		foreach($itens as $key=>$value){
			echo '<input type="hidden" name="itens[]" value="'.$value.'" />';
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
	<?php
	// se estiver confirmada a exclusão
	} else if($_GET['confirm'] == "delete"){
		$itens = $_POST[itens];
		$c = 0;
		foreach($itens as $key=>$value){
			if($c > 0){
				$where = $where." OR id='".$value."'";
			} else {
				$where = "id='".$value."'";
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
		if($module->connection->exec){
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
		foreach($itens as $key=>$value){
			echo '<input type="hidden" name="itens[]" value="'.$value.'" />';
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
	<?php
	// se estiver confirmada a ação
	} else if($_GET['confirm'] == "aprovar"){
		$itens = $_POST[itens];
		$c = 0;
		foreach($itens as $key=>$value){
			if($c > 0){
				$where = $where." OR id='".$value."'";
			} else {
				$where = "id='".$value."'";
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
}

?>