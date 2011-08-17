<?php
/**********************************

	GRAVAR
	
	Variáveis necessárias:
	$_POST -> contendo dados provenientes de formulário

**********************************/

$c = 0;
if(!empty($_POST)) {

	$_POST['frmtitulo_encoded'] = encodeText($_POST['frmtitulo']);

	foreach($_POST as $key=>$value) {
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
					".$module->useThisTable()."
					($sqlcampostr)
				VALUES
					($sqlvalorstr)
				";


		$h1 = 'Criando: '.Aust::getInstance()->getStructureNameById($_GET['aust_node']);
	} else if($_POST['metodo'] == 'edit') {
		$sql = "UPDATE
					".$module->useThisTable()."
				SET
				$sqlcampostr
				WHERE
				id='".$_POST['w']."'
				";
		$h1 = 'Editando: '.Aust::getInstance()->getStructureNameById($_GET['aust_node']);
	}

	$query = $this->module->connection->exec($sql);
	if($query !== false) {
		$resultado = TRUE;

		if($_POST['metodo'] == 'criar') {
			$_POST['w'] = $this->module->connection->conn->lastInsertId();
		}

	} else {
		$resultado = FALSE;
	}

	if($resultado) {
		$status['classe'] = 'sucesso';
		$status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
	} else {
		$status['classe'] = 'insucesso';
		$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.';
	}
	EscreveBoxMensagem($status);

}
?>
