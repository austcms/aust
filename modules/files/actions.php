<?php
		echo 'oijoij';
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
	} elseif(($_GET['confirm'] == "delete") AND (count($_POST['itens']) > 0)){
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
		$mysql = mysql_query($sql);
		$dados = mysql_fetch_array($mysql);

		// se conseguir excluir o arquivo fisicamente, então exclui dados do DB
		try{
			if(unlink('../'.$dados['url'].$dados['arquivo_nome']) or die('Erro ao deletar arquivo, pois ele provavelmente não existe mais. Entre em contato com o administrador.</p>')){

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
			} else {
				$status['classe'] = 'insucesso';
				$status['mensagem'] = '<strong>Erro: </strong> Não foi possível excluir o arquivo.';
				EscreveBoxMensagem($status);
			}
		} catch (Exception $e) {
			echo 'exceção: ', $e->getMessage(), "<br>";
		}
	} elseif(count($_POST['itens']) == 0){
		$status['classe'] = 'alerta';
		$status['mensagem'] = 'Nenhum item selecionado.';
		EscreveBoxMensagem($status);
	}
}

?>