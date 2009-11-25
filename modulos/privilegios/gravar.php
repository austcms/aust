<?php
/*
 * GRAVAR.php
 * Arquivo responsável pela criação das tabelas e configurações de privilégios
 *
 * Variáveis necessárias:
 * $_POST -> contendo dados provenientes de formulário
 *
 * ATENÇÃO: este código pega somente variáveis do $_POST iniciando em frm e grava automaticamente no db
 *
 */


$c = 0;
if(!empty($_POST)){
	foreach($_POST as $key=>$valor){
		// se o argumento $_POST contém 'frm' no início 
		if(strpos($key, 'frm') === 0){
			$sqlcampo[] = str_replace('frm', '', $key);
			$sqlvalor[] = $valor;
			// ajusta os campos da tabela nos quais serão gravados dados
	
			if($_POST[metodo] == 'criar'){
				if($c > 0){
					$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
					$sqlvalorstr = $sqlvalorstr.",'".$valor."'";
				} else {
					$sqlcampostr = str_replace('frm', '', $key);
					$sqlvalorstr = "'".$valor."'";
				}
			} else if($_POST[metodo] == 'editar'){
				if($c > 0){
					$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
				} else {
					$sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
				}
			}
			
			$c++;
		}
	}
	if($_POST[metodo] == 'criar'){
		$sql = "INSERT INTO
					".$modulo->tabela_criar."
					($sqlcampostr)
				VALUES
					($sqlvalorstr)
					";
					
					
		$h1 = 'Criando: '.$aust->leNomeDaEstrutura($_GET[aust_node]);
	} else if($_POST[metodo] == 'editar'){
		$sql = "UPDATE
					".$modulo->tabela_criar."
				SET
					$sqlcampostr
				WHERE
					id='".$_POST[w]."'
					";
		$h1 = 'Editando: '.$aust->leNomeDaEstrutura($_GET[aust_node]);						
	}
    
    //echo $sql;
	if(mysql_query($sql)){
		$resultado = TRUE;
	} else {
		$resultado = FALSE;
	}

	if($resultado){
		$status['classe'] = 'sucesso';
		$status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
	} else {
		$status['classe'] = 'insucesso';
		$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações.';
	}
	EscreveBoxMensagem($status);
	
}
?>