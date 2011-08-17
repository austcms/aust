<?php
]$c = 0;

if(!empty($_POST) AND !empty($_POST["perguntas"]) ){

	$_POST['frmtitulo_encoded'] = encodeText($_POST['frmtitulo']);
	$_POST['frmcategoria'] = $_POST["aust_node"];

	if($_POST['metodo'] == 'create') {
		$_POST['frmadddate'] = date("Y-m-d H:i:s");
	}
	if( $_POST["metodo"] == "criar" ){
		foreach($_POST["perguntas"] as $idPergunta=>$pergunta ){
			if( empty($pergunta) )
				unset($_POST["perguntas"][$idPergunta]);
		}
	}

	/*
	 * Desativa as demais pesquisas caso necessário
	 */
	if( !empty($_POST["frmativo"]) AND $_POST["frmativo"] > 0 ) {
		$sql = "UPDATE
					pesqmkt
				SET
					ativo='0'
				WHERE
					categoria='".$_POST["aust_node"]."'
				";
		$this->module->connection->exec($sql);
		unset($sql);
	}

	foreach($_POST as $key=>$value){
		// se o argumento $_POST contém 'frm' no início
		if(strpos($key, 'frm') === 0) {
			$sqlcampo[] = str_replace('frm', '', $key);
			$sqlvalor[] = $value;
			// ajusta os campos da tabela nos quais serão gravados dados
			$value = addslashes($value);
			if($_POST['metodo'] == 'create') {
				if($c > 0) {
					$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
					$sqlvalorstr = $sqlvalorstr.",'".$value."'";
				} else {
					$sqlcampostr = str_replace('frm', '', $key);
					$sqlvalorstr = "'".$value."'";
				}
			} else if($_POST['metodo'] == 'edit') {
					if($c > 0) {
						$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$value.'\'';
					} else {
						$sqlcampostr = str_replace('frm', '', $key).'=\''.$value.'\'';
					}
				}

			$c++;
		}
	}



	if($_POST['metodo'] == 'create') {
		$sql = "INSERT INTO
									".$this->module->useThisTable()."
									($sqlcampostr)
							VALUES
									($sqlvalorstr)
			";


		$h1 = 'Criando: '.Aust::getInstance()->getStructureNameById($_GET['aust_node']);
	} else if($_POST['metodo'] == 'edit') {
			$sql = "UPDATE
									".$this->module->useThisTable()."
							SET
				$sqlcampostr
							WHERE
									id='".$_POST['w']."'
									";
			$h1 = 'Editando: '.Aust::getInstance()->getStructureNameById($_GET['aust_node']);
	}
	
	/*
	 * Cria a pesquisa no DB
	 */
	$query = $this->module->connection->exec($sql);

	if($query OR $_POST["metodo"] == "edit" ){
		$resultado = TRUE;

		if($_POST['metodo'] == 'create'){
			$_POST['w'] = $this->module->connection->conn->lastInsertId();
			
		}

		/*
		 * ANALISA PERGUNTAS E RESPOSTAS
		 */
		if( !empty($_POST['w']) AND $_POST['metodo'] == "create" ){
			$perguntas = $_POST["perguntas"];
			foreach( $perguntas as $pchave=>$pergunta ){

				if( !empty($pergunta) ){

					/*
					 * Salva a pergunta principal
					 */
					$sqlp = "INSERT INTO pesqmkt_perguntas
								(pesqmkt_id, texto, tipo)
							 VALUES
								('".$_POST["w"]."','".$pergunta."','".$_POST["resposta_tipo"][$pchave]."')
							";

					$queryp = $this->module->connection->exec($sqlp);
					$pid = $this->module->connection->conn->lastInsertId();

					/*
					 * Se é uma pergunta com alternativas
					 */
					if( $_POST["resposta_tipo"][$pchave] == "fechada"
						AND !empty($_POST["resposta"][$pchave]) ){
						/*
						 * Gera sql para salvar alternativas no db
						 */
						/**
						 * @todo -
						 */

						foreach( $_POST["resposta"][$pchave] as $rchave=>$resposta ){
							if( !empty($resposta) )
								$sqlr[] = "('".$pid."','".$resposta."')";
						}

						if( !empty($sqlr) ){
							$sqlrStart[] = "INSERT INTO pesqmkt_respostas
											(pesqmkt_pergunta_id,titulo)
											VALUES
											".implode(",", $sqlr)."
											";
						}

					}

				}
				unset($sqlr);
				unset($sqlp);
			}
		}
		/*
		 * EDIÇÃO DE PERGUNTAS
		 */
		else if( !empty($_POST['w']) AND $_POST['metodo'] == "edit" ){

			/*
			 * Mapeia perguntas existentes no DB
			 */
			$sql = "SELECT
						pp.id, pp.tipo
					FROM
						pesqmkt_perguntas as pp
					WHERE
						pp.pesqmkt_id='".$_POST['w']."'
					";
			$perguntasQuery = $this->module->connection->query($sql, "ASSOC");

			$perguntasExistentes = array();
			if( !empty($perguntasQuery) ){
				foreach( $perguntasQuery as $currentPerguntas ){
					$perguntasExistentes[] = $currentPerguntas["id"];
					$perguntasTiposExistentes[$currentPerguntas["id"]] = $currentPerguntas["tipo"];
				}
			}
			
			foreach( $_POST["perguntas"] as $idPergunta=>$value ){
				if( in_array($idPergunta, $perguntasExistentes) ){

					/*
					 * DELETA A PERGUNTA
					 */
					if( empty($value) ){

						$sqlrStart[] = "DELETE FROM pesqmkt_perguntas WHERE id='".$idPergunta."'";
						$sqlrStart[] = "DELETE FROM pesqmkt_respostas WHERE pesqmkt_pergunta_id='".$idPergunta."'";
						$sqlrStart[] = "DELETE FROM pesqmkt_respostas_textos WHERE pesqmkt_pergunta_id='".$idPergunta."'";
					} else {

						$sqlPergunta = "UPDATE
											pesqmkt_perguntas
										SET
											texto='".$value."',
											tipo='".$_POST["resposta_tipo"][$idPergunta]."'
										WHERE
											id='".$idPergunta."'
										";
						//echo $sqlPergunta;
						$this->module->connection->exec($sqlPergunta);
						if( $_POST["resposta_tipo"][$idPergunta] == "aberta" ){
							$this->module->connection->exec("DELETE FROM pesqmkt_respostas WHERE pesqmkt_pergunta_id='".$idPergunta."'");
						}
					}
				}
				/*
				 * Se uma pergunta não foi encontrada, significa que o usuário
				 * adicionou uma nova.
				 */
				else {
					
					/*
					 * Salva a pergunta principal
					 */
					if( !empty($value) ){
						$sqlp = "INSERT INTO pesqmkt_perguntas
									(pesqmkt_id, texto, tipo)
								 VALUES
									('".$_POST["w"]."','".$value."','".$_POST["resposta_tipo"][$idPergunta]."')
								";

						$queryp = $this->module->connection->exec($sqlp);
						$pid = $this->module->connection->conn->lastInsertId();

						/*
						 * Se é uma pergunta com alternativas
						 */
						if( $_POST["resposta_tipo"][$idPergunta] == "fechada"
							AND !empty($_POST["resposta"][$idPergunta]) ){
							/*
							 * Gera sql para salvar alternativas no db
							 */
							/**
							 * @todo -
							 */

							foreach( $_POST["resposta"][$idPergunta] as $rchave=>$resposta ){
								if( !empty($resposta) ){
									$sqlr[] = "('".$pid."','".$resposta."')";
								}
							}

							if( !empty($sqlr) ){
								$sqlrStart[] = "INSERT INTO pesqmkt_respostas
												(pesqmkt_pergunta_id,titulo)
												VALUES
												".implode(",", $sqlr)."
												";
							}

						}
					}

				}
			}

			if( empty($_POST["resposta"]) )
				$_POST["resposta"] = array();
			foreach($_POST["resposta"] as $idPergunta=>$value){

				/*
				 * Pergunta existe, edita
				 */
				if( in_array($idPergunta, $perguntasExistentes) ){
					/*
					 * Modifica informações da pergunta
					 */

					
					/*
					 * Verifica quais respostas esta pergunta tem.
					 */
					$sql = "SELECT
								pr.id
							FROM
								pesqmkt_respostas as pr
							WHERE
								pr.pesqmkt_pergunta_id='".$idPergunta."'
							";
					$respostaQuery = $this->module->connection->query($sql, "ASSOC");

					$respostaExistentes = array();
					if( !empty($respostaQuery) ){
						foreach( $respostaQuery as $currentResposta ){
							$respostaExistentes[] = $currentResposta["id"];
						}
					}
					/*
					 * Loop por cada resposta da pergunta de id $idPergunta
					 */
					foreach( $value as $pChave=>$pValor ){

						/*
						 * Se a alternativa está vazia, sim é
						 */

						if( in_array($pChave, $respostaExistentes) ){

							/*
							 * Exclui respostas com título vazio
							 */
							if( empty($pValor) ){
								$sqlrStart[] = "DELETE FROM pesqmkt_respostas WHERE id='".$pChave."'";
							} else {
								$sqlrStart[] = "UPDATE
													pesqmkt_respostas
												SET
													titulo='".$pValor."'
												WHERE
													id='".$pChave."'
												";
							}
						} else {
							if( !empty($pValor) ){
								$sqlrStart[] = "INSERT INTO
													pesqmkt_respostas
												(pesqmkt_pergunta_id,titulo)
												VALUES
													('".$idPergunta."','".$pValor."')
												";
							}
						}
					}
				}
				/*
				 * Pergunta não existe, insert
				 */
				else {


				}
			}
		}

		if( !empty($sqlrStart) ){
			foreach( $sqlrStart as $sqlRespostas ){
				$respostasSQL[] = $this->module->connection->exec($sqlRespostas);
			}
		}

	} else {
		$resultado = FALSE;
	}

	if($resultado){
			$status['classe'] = 'sucesso';
			$status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
	} else {
			$status['classe'] = 'insucesso';
			$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.';
	}
	EscreveBoxMensagem($status);
	
} else if( empty($_POST["frmtitulo"]) ) {
	$status['classe'] = 'insucesso';
	$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.';
	EscreveBoxMensagem($status);

}
?>

<br />
<p>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
